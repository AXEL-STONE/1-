(function(window){
    'use strict';

    if (window.JCUserList)
        return;

    window.JCUserList = function(arParams)
    {
        this.stop = false;
        this.progressBar = BX('progress-export-bar');
        this.progressContainer = BX('progress-export');
        this.stopExportButton = BX('stop-export');
        this.startExportButton = BX('start-export');
        this.successExport = BX('success-export');
        this.fileSizeContainer = BX('file-size-container');
        this.exportUser = {
            path: arParams.componentPath+'/ajax.php',
            params: {
                AJAX: 'Y',
                parameters: arParams.parameters,
                template: arParams.template
            }
        };
        BX.ready(BX.delegate(this.init, this));
    }

    window.JCUserList.prototype = {
        init: function() {
            BX.bind(this.startExportButton, 'click', BX.proxy(this.startExport, this));
            BX.bind(this.stopExportButton, 'click', BX.proxy(this.stopExport, this));
        },
        startExport: function() {
            BX.ajax({
                method: 'POST',
                dataType: 'json',
                url: this.exportUser.path,
                data: this.exportUser.params,
                onsuccess: BX.proxy(this.onsuccess, this)
            });
        },
        stopExport: function() {
            this.stop = true;
        },
        onsuccess: function (data) {
            if(!data.stop && !this.stop) {
                this.exportUser.params.page = data.page;
                this.stopExportButton.style.display = 'inline-block';
                this.progressContainer.style.display = 'flex';
                this.startExportButton.style.display = 'none';
                this.fileSizeContainer.style.display = 'block';
                this.fileSizeContainer.innerHTML = data.sizeFile;
                this.progressBar.style.width = data.percent+'%';
                this.progressBar.style.ariaValuenow = data.percent;
                this.progressBar.innerHTML = data.percent+'%';
                this.startExport();
            }
            if(this.stop) {
                this.stopExportButton.style.display = 'none';
                this.progressContainer.style.display = 'none';
                this.startExportButton.style.display = 'inline-block';
                this.fileSizeContainer.style.display = 'none';
                this.fileSizeContainer.innerHTML = '';
                this.progressBar.style.width = '0%';
                this.progressBar.style.ariaValuenow = 0;
                this.progressBar.innerHTML = '0%';
                this.stop = false;
                this.exportUser.params.page = 1;
            }
            if(data.stop) {
                this.successExport.style.display = 'block';
                this.stopExportButton.style.display = 'none';
                this.progressContainer.style.display = 'none';
                this.fileSizeContainer.style.display = 'none';
                this.fileSizeContainer.innerHTML = '';
                window.location = data.file;
            }
        }
    }
})(window);