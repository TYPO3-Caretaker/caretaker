/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

Ext.namespace('tx','tx.caretaker');

tx.caretaker.overview_application = function() {

    tx.caretaker.node_information = new Ext.Panel({
        id              : "node-info",
        html            : "node info",
        autoHeight      : true,
        autoLoad        : tx.caretaker.back_path + 'ajax.php?ajaxID=tx_caretaker::nodeinfo&node=' + tx.caretaker.node_info.id
    });

    tx.caretaker.node_children = new Ext.Panel ({
        id              : "node-children",
        title           : "Node Children",
        html            : "foo",
        autoHeight      : true
    });

    tx.caretaker.node_charts = new Ext.TabPanel ({
        activeTab       : 0,
        id              : "node-charts",
        enableTabScroll : true,
        height          : 430,
        title           : "Chart",
        items:[
           {
                title:'12 h',
                autoLoad  : tx.caretaker.back_path + 'ajax.php?ajaxID=tx_caretaker::nodegraph&node=' + tx.caretaker.node_info.id + '&duration= ' +  (60*60*12)
            },{
                title:'24 h',
                autoLoad  : tx.caretaker.back_path + 'ajax.php?ajaxID=tx_caretaker::nodegraph&node=' + tx.caretaker.node_info.id + '&duration= ' +  (60*60*24)
            },{
                title:'48 h',
                autoLoad  : tx.caretaker.back_path + 'ajax.php?ajaxID=tx_caretaker::nodegraph&node=' + tx.caretaker.node_info.id + '&duration= ' +  (60*60*48)
            },{
                title:'7 Days',
                autoLoad  : tx.caretaker.back_path + 'ajax.php?ajaxID=tx_caretaker::nodegraph&node=' + tx.caretaker.node_info.id + '&duration= ' +  (60*60*24*7)
            },{
                title:'1 Month',
                autoLoad  : tx.caretaker.back_path + 'ajax.php?ajaxID=tx_caretaker::nodegraph&node=' + tx.caretaker.node_info.id + '&duration= ' +  (60*60*24*31)
            },{
                title:'3 Month',
                autoLoad  : tx.caretaker.back_path + 'ajax.php?ajaxID=tx_caretaker::nodegraph&node=' + tx.caretaker.node_info.id + '&duration= ' +  (60*60*34*93)
            }
        ]
    });

    tx.caretaker.node_log = new Ext.Panel ({
            id              : "node-log",
            title           : "Log",
            html            : "soon to come with a fancy ExtJS GridPanel",
            autoHeight      : true,
            collapsible     : true
    });

    tx.caretaker.refreschNode = function (){
        Ext.Ajax.request({
           url: tx.caretaker.back_path + 'ajax.php',
           success: tx.caretaker.refreschSuccessMessage,
           failure: tx.caretaker.refreschFailureMessage,
           params: { 
               ajaxID: 'tx_caretaker::noderefresh',
               node:   tx.caretaker.node_info.id,
               force:  0
            }
        });
    };

    tx.caretaker.refreschNodeForced = function (){
         Ext.Ajax.request({
           url: tx.caretaker.back_path + 'ajax.php',
           success: tx.caretaker.refreschSuccessMessage,
           failure: tx.caretaker.refreschFailureMessage,
           params: {
               ajaxID: 'tx_caretaker::noderefresh',
               node:   tx.caretaker.node_info.id,
               force:  1
            }
        });
        // Ext.Ajax.on('requestcomplete',  this.refreschSuccessMessage, this);
        // Ext.Ajax.on('requestexception', this.refreschSuccessMessage, tx.caretaker);
    };

    tx.caretaker.refreschSuccessMessage = function (response, opts){
        tx.caretaker.showUpdateLog(response.responseText);
        var node_info_panel = Ext.getCmp('node-info');
        node_info_panel.load( tx.caretaker.back_path + 'ajax.php?ajaxID=tx_caretaker::nodeinfo&node=' + tx.caretaker.node_info.id);
    }
    
    tx.caretaker.refreschFailureMessage = function (response, opts){
        tx.caretaker.showUpdateLog(response.responseText)
        var node_info_panel = Ext.getCmp('node-info');
        node_info_panel.load( tx.caretaker.back_path + 'ajax.php?ajaxID=tx_caretaker::nodeinfo&node=' + tx.caretaker.node_info.id);
    }

    tx.caretaker.showUpdateLog = function(log){

        var win = new Ext.Window({
            autoHeight  : true,
            width       : 600,
            modal       : true,                                                  // 1
            title       : 'Refresh Node Log',
            plain       : true,
            border      : false,
            resizable   : false,                                                 // 2
            draggable   : false,                                                 // 3
            closable    : false,                                                 // 4
            buttonAlign : 'center',
            items       : {
                    xtype: "panel",
                    autoHeight  : true,
                    html : log
                },
            buttons     : [{
                    text    : 'OK',
                    handler : function() {
                       win.close();
                    }
                }]
        });
        win.show();
    }

    tx.caretaker.editNode = function (){
        if (tx.caretaker.node_info.type_lower != 'root'){
            var url = tx.caretaker.path_typo3 + 'alt_doc.php?edit[tx.caretaker.' + tx.caretaker.node_info.type_lower + '][' + tx.caretaker.node_info.uid + ']=edit&returnUrl=' + tx.caretaker.back_url;
            window.location.href = url;
        } else {
            Ext.MessageBox.alert('Sorry', 'The root node cannot be edited!');
        }
    };

    tx.caretaker.enableNode = function() {
        if (tx.caretaker.node_info.hidden == 1 && tx.caretaker.node_info.type_lower != 'root'){
            var url = tx.caretaker.path_typo3 + 'tce_db.php?&data[tx.caretaker.' + tx.caretaker.node_info.type_lower + '][' + tx.caretaker.node_info.uid + '][hidden]=0&redirect=' + tx.caretaker.back_url;
            window.location.href = url;
        } else {
            Ext.MessageBox.alert('Sorry', 'The node is already enabled');
        }
    };

    tx.caretaker.disableNode = function() {
        if (tx.caretaker.node_info.hidden == 0 && tx.caretaker.node_info.type_lower != 'root'){
            var url = tx.caretaker.path_typo3 + 'tce_db.php?&data[tx.caretaker.' + tx.caretaker.node_info.type_lower + '][' + tx.caretaker.node_info.uid + '][hidden]=1&redirect=' + tx.caretaker.back_url;
            window.location.href = url;
        } else {
            Ext.MessageBox.alert('Sorry', 'The node is already hidden');
        }
    };

    tx.caretaker.node_toolbar = new Ext.Toolbar ({
            layout : "toolbar",
            items :  [
                    {
                            text    : "Refresh",
                            icon    : "../res/icons/arrow_refresh_small.png",
                            handler : tx.caretaker.refreschNode
                    },
                    {
                            text    : "Refresh forced",
                            icon    : "../res/icons/arrow_refresh.png",
                            handler : tx.caretaker.refreschNodeForced
                    },
                    "-",
                    {
                            text    : "Edit",
                            icon    : "../res/icons/pencil.png",
                            disabled: (tx.caretaker.node_info.type_lower=='root')?true:false,
                            handler :  tx.caretaker.editNode
                    },
                    "->",
                    {
                            text    : "Enable",
                            disabled: (tx.caretaker.node_info.hidden==0 || tx.caretaker.node_info.type_lower=='root')?true:false,
                            icon    : "../res/icons/lightbulb.png",
                            handler : tx.caretaker.enableNode
                    },
                    {
                            text    : "Disable",
                            icon    : "../res/icons/lightbulb_off.png",
                            disabled: (tx.caretaker.node_info.hidden==1 || tx.caretaker.node_info.type_lower=='root')?true:false,
                            handler : tx.caretaker.disableNode
                    }

            ]
    });

    tx.caretaker.view = new Ext.Viewport({
            layout: "fit",
            items: {
                    xtype    : "panel",
                    id       : "node",
                    title    : '' +  tx.caretaker.node_info.title + ' (' + tx.caretaker.node_info.type + ')' ,
                    iconCls  : "icon-caretaker-type-" + tx.caretaker.node_info.type_lower,
                    tbar     : tx.caretaker.node_toolbar,
                    items    : [
                        tx.caretaker.node_information,
                        tx.caretaker.node_charts,
                        //tx.caretaker.node_children,
                        //tx.caretaker.node_log
                    ]
            }
    });
}
		
