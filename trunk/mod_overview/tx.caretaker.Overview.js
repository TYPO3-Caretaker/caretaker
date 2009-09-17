/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

tx_caretaker_Overview = function() {

    var node_info = tx_caretaker_node_info;
    var back_path = tx_caretaker_back_path;

    Ext.MessageBox.maxWidth = 700;
    Ext.MessageBox.minWidth = 500;


    tx.caretaker.node_info = new Ext.Panel({
        id              : "node-info",
        html            : "node info",
        autoHeight      : true,
        autoLoad        : back_path + 'ajax.php?ajaxID=tx_caretaker::nodeinfo&node=' + node_info.id
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
        height          : 400,
        title           : "Chart",
        items:[
           {
                title:'12 h',
                autoLoad  : back_path + 'ajax.php?ajaxID=tx_caretaker::nodegraph&node=' + node_info.id + '&duration= ' +  (60*60*12)
            },{
                title:'24 h',
                autoLoad  : back_path + 'ajax.php?ajaxID=tx_caretaker::nodegraph&node=' + node_info.id + '&duration= ' +  (60*60*24)
            },{
                title:'48 h',
                autoLoad  : back_path + 'ajax.php?ajaxID=tx_caretaker::nodegraph&node=' + node_info.id + '&duration= ' +  (60*60*48)
            },{
                title:'7 Days',
                autoLoad  : back_path + 'ajax.php?ajaxID=tx_caretaker::nodegraph&node=' + node_info.id + '&duration= ' +  (60*60*24*7)
            },{
                title:'1 Month',
                autoLoad  : back_path + 'ajax.php?ajaxID=tx_caretaker::nodegraph&node=' + node_info.id + '&duration= ' +  (60*60*24*31)
            },{
                title:'3 Month',
                autoLoad  : back_path + 'ajax.php?ajaxID=tx_caretaker::nodegraph&node=' + node_info.id + '&duration= ' +  (60*60*34*93)
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
           url: tx_caretaker_back_path + 'ajax.php',
           success: tx.caretaker.refreschSuccessMessage,
           failure: tx.caretaker.refreschFailureMessage,
           params: { 
               ajaxID: 'tx_caretaker::noderefresh',
               node:   tx_caretaker_node_info.id,
               force:  0
            }
        });
    };

    tx.caretaker.refreschNodeForced = function (){
         Ext.Ajax.request({
           url: tx_caretaker_back_path + 'ajax.php',
           success: tx.caretaker.refreschSuccessMessage,
           failure: tx.caretaker.refreschFailureMessage,
           params: {
               ajaxID: 'tx_caretaker::noderefresh',
               node:   tx_caretaker_node_info.id,
               force:  1
            }
        });
    };

    tx.caretaker.refreschSuccessMessage = function (response, opts){
        var node_info = Ext.getCmp('node-info');
        node_info.load( tx_caretaker_back_path + 'ajax.php?ajaxID=tx_caretaker::nodeinfo&node=' + tx_caretaker_node_info.id);
        Ext.MessageBox.alert(response.responseText);
    }
    
    tx.caretaker.refreschFailureMessage = function (response, opts){
        var node_info = Ext.getCmp('node-info');
        node_info.load( tx_caretaker_back_path + 'ajax.php?ajaxID=tx_caretaker::nodeinfo&node=' + tx_caretaker_node_info.id);


        Ext.MessageBox.alert(response.responseText);

    }

    tx.caretaker.editNode = function (){
        if (tx_caretaker_node_info.type_lower != 'root'){
            var url = tx_caretaker_path_typo3 + 'alt_doc.php?edit[tx_caretaker_' + tx_caretaker_node_info.type_lower + '][' + tx_caretaker_node_info.uid + ']=edit&returnUrl=' + tx_caretaker_back_url;
            window.location.href = url;
        } else {
            Ext.MessageBox.alert('Sorry', 'The root node cannot be edited!');
        }
    };

    tx.caretaker.enableNode = function() {
        if (tx_caretaker_node_info.hidden == 1 && tx_caretaker_node_info.type_lower != 'root'){
            var url = tx_caretaker_path_typo3 + 'tce_db.php?&data[tx_caretaker_' + tx_caretaker_node_info.type_lower + '][' + tx_caretaker_node_info.uid + '][hidden]=0&redirect=' + tx_caretaker_back_url;
            window.location.href = url;
        } else {
            Ext.MessageBox.alert('Sorry', 'The node is already enabled');
        }
    };

    tx.caretaker.disableNode = function() {
        if (tx_caretaker_node_info.hidden == 0 && tx_caretaker_node_info.type_lower != 'root'){
            var url = tx_caretaker_path_typo3 + 'tce_db.php?&data[tx_caretaker_' + tx_caretaker_node_info.type_lower + '][' + tx_caretaker_node_info.uid + '][hidden]=1&redirect=' + tx_caretaker_back_url;
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
                            disabled: (tx_caretaker_node_info.type_lower=='root')?true:false,
                            handler :  tx.caretaker.editNode
                    },
                    "->",
                    {
                            text    : "Enable",
                            disabled: (tx_caretaker_node_info.hidden==0 || tx_caretaker_node_info.type_lower=='root')?true:false,
                            icon    : "../res/icons/lightbulb.png",
                            handler : tx.caretaker.enableNode
                    },
                    {
                            text    : "Disable",
                            icon    : "../res/icons/lightbulb_off.png",
                            disabled: (tx_caretaker_node_info.hidden==1 || tx_caretaker_node_info.type_lower=='root')?true:false,
                            handler : tx.caretaker.disableNode
                    }

            ]
    });

    tx.caretaker.view = new Ext.Viewport({
            layout: "fit",
            items: {
                    xtype    : "panel",
                    id       : "node",
                    title    : '' +  node_info.title + ' (' + node_info.type + ')' ,
                    iconCls  : "icon-caretaker-type-" + node_info.type_lower,
                    tbar     : tx.caretaker.node_toolbar,
                    items    : [
                        tx.caretaker.node_info,
                        tx.caretaker.node_charts,
                        //tx.caretaker.node_children,
                        //tx.caretaker.node_log
                    ]
            }
    });
}
		
