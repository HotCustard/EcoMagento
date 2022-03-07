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
    'jquery',
    'Magento_Ui/js/form/element/file-uploader',
    'uiRegistry'
], function ($, Element, uiRegistry) {
    'use strict';

    return Element.extend({
        defaults: {
            fileInputName: ''
        },

        /**
         * Adds provided file to the files list.
         *
         * @param {Object} file
         * @returns {FileUploder} Chainable.
         */
        addFile: function (file) {
            var processedFile = this.processFile(file),
                tmpFile = [],
                resultFile = {
                'file': processedFile.file,
                'name': processedFile.name,
                'size': processedFile.size,
                'url': processedFile.url,
                'status': processedFile.status ? processedFile.status : 'new'
            };

            tmpFile[0] = resultFile;
           
            this.isMultipleFiles ?
                this.value.push(tmpFile) :
                this.value(tmpFile);

            return this;
        },
        sizeMb: function (bytes,type) {

            if (type == 'M') {
                return(bytes / 1048576);
            } else if (type == 'G') {
            return(bytes / 1073741824);
            }
            
    
    
        },
        onBeforeFileUpload: function (e, data) {
            var file     = data.files[0],
                allowed  = this.isFileAllowed(file);


             var field1 = uiRegistry.get('index = maxvalues');
            
           //  console.log(field1);
            var arr = [];
            $("#"+field1.uid+" > option").each(function () {
               arr.push(this.value);
            });

             var file_type = (arr[0]);
          var post_type = (arr[1]);

          var file_max = parseInt(arr[0].slice(0,-1));
          var post_max = parseInt(arr[1].slice(0,-1));


        var f_type = file_type[file_type.length-1];
        var p_type = file_type[file_type.length-1];

            var file_size = this.sizeMb(file.size,f_type);
            var post_size = this.sizeMb(file.size,p_type);
            
         
         var valid = false;
         if ((file_size > file_max) || (post_size > post_max)) {
         valid= false;
            allowed.message = "<b>Error</b> : Your Upload File Size :"+this.formatSize(file.size)+" is Greater than upload_max_filesize :"+file_type+" Or post_max_size :"+post_type+" in php.ini";
         } else {
            valid = true;
         }
           

            if (allowed.passed && valid) {
                $(e.target).fileupload('process', data).done(function () {
                    data.submit();
                });
            } else {
                this.notifyError(allowed.message);
            }
        }
    });
});
