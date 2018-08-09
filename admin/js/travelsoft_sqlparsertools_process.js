/* 
 * travelsoft_sqlparsertools_process.js
 * 
 * @author dimabresky
 */

(function (window) {

    var bx = window.BX;
    var php_vars = window.phpVars;

    bx.ready(function () {

        "use strict";

        var progress_area = bx("progress-area");

        /** 
         * @param {Object} data
         * @param {Function} onsuccess
         * @param {Function} onfailure
         * @returns {undefined}
         */
        function sendRequest(data, onsuccess, onfailure) {
            bx.showWait();
            data.sessid = bx.bitrix_sessid();
            bx.ajax({
                url: '/local/modules/travelsoft.sqlparsertools/admin/ajax/processing.php',
                data: data,
                method: 'POST',
                dataType: 'json',
                timeout: 99999999,
                async: true,
                processData: true,
                scriptsRunFirst: false,
                emulateOnload: false,
                start: true,
                cache: false,
                onsuccess: function (resp) {
                    bx.closeWait();
                    if (!onsuccess(resp)) {
                        return;
                    }
                },
                onfailure: onfailure
            });

        }

        function onSuccess(resp) {
            
            bx.closeWait();
            if (resp.html !== "") {
                
                progress_area.innerHTML = resp.html;
            }

            if (resp.error || resp.next_action === "") {
                return false;
            }

            sendRequest({
                action: resp.next_action,
                parameters: {}
            }, onSuccess, triggerError );
            
            return true;
        }

        function triggerError() {
            alert("Ooops, comrads. Server problem. Please, try again later");
//            window.location = `/bitrix/admin/travelsoft_sqlparsertools.php?lang=${php_vars.LANGUAGE_ID}`;
        }

        sendRequest({
            action: "import_sql",
            parameters: {
                sql_file_name: php_vars.sql_file_name
            }
        }, onSuccess, triggerError);
    });
})(window);


