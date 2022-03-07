/**
* FME Extensions
*
* NOTICE OF LICENSE
*
* This source file is subject to the fmeextensions.com license that is
* available through the world-wide-web at this URL:
* https://www.fmeextensions.com/LICENSE.txt
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this extension to newer
* version in the future.
*
* @category FME
* @package FME_Productattachments
* @copyright Copyright (c) 2019 FME (http://fmeextensions.com/)
* @license https://fmeextensions.com/LICENSE.txt
*/
define([
    './column',
    'jquery',
    'ko' ,
    'underscore',
    'mageUtils',
    'Magento_Ui/js/modal/alert',
    'Magento_Ui/js/lib/validation/validator',
    'Magento_Ui/js/form/element/abstract',
    'jquery/file-uploader'
], function (Column, $, ko, _, utils, uiAlert, validator, Element) {
    'use strict';

    var file_type = "";
        var post_type = "";

    var column = Column.extend({

        
        defaults: {

            bodyTmpl: 'FME_Productattachments/cms/thumbnail.html',
            
            
        },
        
        
    
        getId: function (row) {

            return row[this.index + '_id'];
            
        },
        getUploadUrl: function (row) {
            return row[this.index + '_url'];
        },
        getName: function (row) {
           file_type = (row[this.index + '_file_max']);
            post_type = (row[this.index + '_post_max']);
            return row[this.index + '_name'];
        },
        getSrc: function (row) {
          //  alert("test2");
            return row[this.index + '_src'];
        },
        getFormkey: function (row) {
            
            return row[this.index + '_formkey']
        },
        getOrigSrc: function (row) {
           // alert("test2");
            return row[this.index + '_orig_src'];
        },
        getLink: function (row) {
          //  alert("test2");
            return row[this.index + '_link'];
        },
        getAlt: function (row) {
          
          // $('#upl').html('');
          // alert($('#upl').html());
          // $('upl').html('');
            //return row[this.index + '_alt']
        },
        isPreviewAvailable: function () {
         //   alert("test2");
            return this.has_preview || false;
        },
       
        getFieldHandler: function (row) {
           
            if (this.isPreviewAvailable()) {
              //  return this.preview.bind(this, row);
            }
        }
    });


     ko.bindingHandlers.fileUpload = {
        init: function (element, valueAccessor, allBindings, viewModel, bindingContext) {
            $(element).fileupload({
                dataType: 'json',
                sequentialUploads: true,
                maxFileSize: 200,
                dropZone: $(element).siblings('.drop-zone'),
                add: function (e, data) {
                        $(this).fileupload('process', data).done(function () {
                            var fileSize, maxFileSize;
                            var element = e.target;
                            var errorMessage = null;
                            var valid = false;

                            var element = e.target;

                            $.each(data.files, function (index, file) {


                            ////////  Upload file Max check
                            var file_max = parseInt(file_type.slice(0,-1));
                            var post_max = parseInt(post_type.slice(0,-1));


                            var f_type = file_type[file_type.length-1];
                            var p_type = file_type[file_type.length-1];


                            var file_size = null;
                            var post_size = null;

                            if (f_type == 'M') {
                                file_size = (file.size / 1048576);
                            } else if (f_type == 'G') {
                            file_size = (file.size / 1073741824);
                            }


                            if (p_type == 'M') {
                                post_size = (file.size / 1048576);
                            } else if (p_type == 'G') {
                            post_size = (file.size / 1073741824);
                            }


                            if ((file_size > file_max) || (post_size > post_max)) {
                            var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'],i,f_size;
                                i = window.parseInt(Math.floor(Math.log(file.size) / Math.log(1024)));
                                f_size = Math.round(file.size / Math.pow(1024, i), 2) + ' ' + sizes[i];
                                valid= false;
                                errorMessage = "<b>Error</b> : Your Upload File Size :"+(f_size)+" is Greater than upload_max_filesize :"+file_type+" Or post_max_size :"+post_type+" in php.ini";
                            } else {
                                valid = true;
                            }



                        /////////  Upload file Max check



                        fileSize = file.size;

                        if (typeof fileSize == "undefined") {
                            errorMessage = 'We could not detect a size.';
                        }

                    });
                            if (errorMessage === null && valid) {
                                data.submit();
                            } else {
                               uiAlert({
                                   content: errorMessage
                               });
                           // alert({content: errorMessage});
                       }
                   });
                    },
                progressall: function (event, data) {
                    var element = event.target;
                    this.spinner = $(element).closest('.upload-attachment').find('.spinner');
                    this.spinner.show();
                },
                done: function (event, data) {
                    this.spinner.hide();
                    if (data.result && (data.result.hasOwnProperty('errorcode') || data.result.hasOwnProperty('error')) && data.result.error !=0) {
                        var alertMessage = data.result.hasOwnProperty('message') ? data.result.message : data.result.error;
                     //   alert({content: alertMessage});
                       uiAlert({
                        content: data.result.error
                    });
                     //  alert(data.result.error);
                    } else {
                     //   alert(JSON.stringify(data.result));
                        var element = event.target;
                     //   alert(data.result.product_id);
                        var attachment = $('<span/>');
                        attachment.append($('<a/>').attr('href', data.result.url).text(data.result.name));

                        attachment.clone().appendTo($('#att-list-' + data.result.cms_id));
                        $('<br/>').appendTo($('#att-list-' + data.result.cms_id));

                        var succ = $("<div/>").addClass('success-upload-image');
                        $(element).closest('.upload-attachment').append(succ);
                        succ.fadeOut(5200);
                    }

                },
                fail: function (e, data) {

                    this.spinner.hide();
                     uiAlert({
                   content: 'Something Went Wrong While Uploading This File'
               });

                }
            });
        },
        update: function (element, valueAccessor, allBindings, viewModel, bindingContext) {
            $(document).bind('dragover', function (e) {
                e.preventDefault();
                //initDropZone(e);
            });
        }
    };


    return column;

    
});
