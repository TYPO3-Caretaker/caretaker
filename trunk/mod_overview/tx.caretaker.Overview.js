/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

Ext.namespace('tx','tx.caretaker');


tx.caretaker.overview = function() {



   var node_information = new Ext.Panel({
        id              : "node-info",
        html            : "node info",
        autoHeight      : true,
        autoLoad        : tx.caretaker.back_path + 'ajax.php?ajaxID=tx_caretaker::nodeinfo&node=' + tx.caretaker.node_info.id
    });

   var node_children = new Ext.Panel ({
        id              : "node-children",
        title           : "Node Children",
        html            : "foo",
        autoHeight      : true
    });

    var node_charts = new Ext.TabPanel ({
        activeTab       : 0,
        id              : "node-charts",
        enableTabScroll : true,
		collapsible     : true,
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

    // create the Data Store
    var nodelog_store = new Ext.data.JsonStore({
        root: 'logItems',
        totalProperty: 'totalCount',
        idProperty: 'timestamp',
        remoteSort: false,
        fields: [
            'num',
            'title',
            {name: 'timestamp', mapping: 'timestamp', type: 'date', dateFormat: 'timestamp'},
            'stateinfo',
            'message',
            'state'
        ],
        proxy: new Ext.data.HttpProxy({
            url: tx.caretaker.back_path + 'ajax.php?ajaxID=tx_caretaker::nodelog&node=' + tx.caretaker.node_info.id
        })
    });
    
    // nodelog_store.setDefaultSort('lastpost', 'desc');

    var node_log = new Ext.grid.GridPanel({
        collapsed: true,
        collapsible: true,
        title:'Log',
        store: nodelog_store,
        trackMouseOver:false,
        disableSelection:true,
        loadMask: true,
	
        // grid columns
        columns:[{
            header: "timestamp",
            dataIndex: 'timestamp'
        },{
            header: "stateinfo",
            dataIndex: 'stateinfo'
        },{
            header:'message',
            dataIndex: 'message'
        }],
        
        // customize view config
        viewConfig: {
            forceFit:true,
            enableRowBody:true,
            showPreview:true,
			getRowClass: function(record, index) {
				var state = record.get('state');
				switch (state) {
					case 0:
						return 'tx_caretaker_node_logrow tx_caretaker_node_logrow_OK';
					case 1:
						return 'tx_caretaker_node_logrow tx_caretaker_node_logrow_WARNING';
					case 2:
						return 'tx_caretaker_node_logrow tx_caretaker_node_logrow_ERROR';
					default:
						return 'tx_caretaker_node_logrow tx_caretaker_node_logrow_UNDEFINED';					
				}
			}
        },
		
  

        // paging bar on the bottom
        bbar: new Ext.PagingToolbar({
            pageSize: 10,
            store: nodelog_store,
            displayInfo: true,
            displayMsg: 'Displaying topics {0} - {1} of {2}',
            emptyMsg: "No topics to display"
        })
    });


    // trigger the data store load
    nodelog_store.load({params:{start:0, limit:10}}); 


    var refreschNode = function (){
        Ext.Ajax.request({
           url: tx.caretaker.back_path + 'ajax.php',
           success: refreschSuccess,
           failure: refreschFailure,
           params: { 
               ajaxID: 'tx_caretaker::noderefresh',
               node:   tx.caretaker.node_info.id,
               force:  0
            }
        });
    };

    var refreschNodeForced = function (){
         Ext.Ajax.request({
           url: tx.caretaker.back_path + 'ajax.php',
           success: refreschSuccess,
           failure: refreschFailure,
           params: {
               ajaxID: 'tx_caretaker::noderefresh',
               node:   tx.caretaker.node_info.id,
               force:  1
            }
        });
        // Ext.Ajax.on('requestcomplete',  this.refreschSuccessMessage, this);
        // Ext.Ajax.on('requestexception', this.refreschSuccessMessage, tx.caretaker);
    };

    var refreschSuccess= function (response, opts){
        var node_info_panel = Ext.getCmp('node-info');
        node_info_panel.load( tx.caretaker.back_path + 'ajax.php?ajaxID=tx_caretaker::nodeinfo&node=' + tx.caretaker.node_info.id);
        showUpdateLog(response.responseText);
		refreshTree();
    }
    
    var refreschFailure = function (response, opts){
        var node_info_panel = Ext.getCmp('node-info');
        node_info_panel.load( tx.caretaker.back_path + 'ajax.php?ajaxID=tx_caretaker::nodeinfo&node=' + tx.caretaker.node_info.id);
        showUpdateLog(response.responseText);
		refreshTree();
    }

	var showUpdateLog = function(log){

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
					maxHeight:400,
					minHeight:60,
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
	
	var showUpdateLogTop = showUpdateLog.createDelegate(top);

	
    var editNode = function (){
        if (tx.caretaker.node_info.type_lower != 'root'){
			refreshTree(1000);
            var url = tx.caretaker.path_typo3 + 'alt_doc.php?edit[tx_caretaker_' + tx.caretaker.node_info.type_lower + '][' + tx.caretaker.node_info.uid + ']=edit&returnUrl=' + tx.caretaker.back_url;
            window.location.href = url;
        } else {
             top.Ext.MessageBox.alert('Sorry', 'The root node cannot be edited!');
        }
    };

    var enableNode = function() {
        if (tx.caretaker.node_info.hidden == 1 && tx.caretaker.node_info.type_lower != 'root'){
			refreshTree(1000);
            var url = tx.caretaker.path_typo3 + 'tce_db.php?&data[tx_caretaker_' + tx.caretaker.node_info.type_lower + '][' + tx.caretaker.node_info.uid + '][hidden]=0&redirect=' + tx.caretaker.back_url;
            window.location.href = url;
        } else {
            top.Ext.MessageBox.alert('Sorry', 'The node is already enabled');
        }
    };

    var disableNode = function() {
        if (tx.caretaker.node_info.hidden == 0 && tx.caretaker.node_info.type_lower != 'root'){
			refreshTree(1000);
            var url = tx.caretaker.path_typo3 + 'tce_db.php?&data[tx_caretaker_' + tx.caretaker.node_info.type_lower + '][' + tx.caretaker.node_info.uid + '][hidden]=1&redirect=' + tx.caretaker.back_url;
            window.location.href = url;
        } else {
            top.Ext.MessageBox.alert('Sorry', 'The node is already hidden');
        }
    };

	var refreshTree = function (defer){
		if (top.content.nav_frame && top.content.nav_frame.tx.caretaker.view){
			var cartaker_tree = top.content.nav_frame.tx.caretaker.view.get('cartaker-tree');
			if (!defer) defer = false;
			cartaker_tree.reloadTreeDeferred(defer);
		}
	}

    tx.caretaker.node_toolbar = new Ext.Toolbar ({
            layout : "toolbar",
            items :  [
                    {
                            text    : "Refresh",
                            icon    : "../res/icons/arrow_refresh_small.png",
                            handler : refreschNode
                    },
                    {
                            text    : "Refresh forced",
                            icon    : "../res/icons/arrow_refresh.png",
                            handler : refreschNodeForced
                    },
                    "-",
                    {
                            text    : "Edit",
                            icon    : "../res/icons/pencil.png",
                            disabled: (tx.caretaker.node_info.type_lower=='root')?true:false,
                            handler : editNode
                    },
                    "->",
                    {
                            text    : "Enable",
                            disabled: (tx.caretaker.node_info.hidden==0 || tx.caretaker.node_info.type_lower=='root')?true:false,
                            icon    : "../res/icons/lightbulb.png",
                            handler : enableNode
                    },
                    {
                            text    : "Disable",
                            icon    : "../res/icons/lightbulb_off.png",
                            disabled: (tx.caretaker.node_info.hidden==1 || tx.caretaker.node_info.type_lower=='root')?true:false,
                            handler : disableNode
                    }

            ]
    });

    var view = new Ext.Viewport({
            layout: "fit",
            items: {
                    xtype    : "panel",
                    id       : "node",
                    autoScroll: true,
                    title    : '' +  tx.caretaker.node_info.title + ' (' + tx.caretaker.node_info.type + ')' ,
                    iconCls  : "icon-caretaker-type-" + tx.caretaker.node_info.type_lower,
                    tbar     : tx.caretaker.node_toolbar,
                    items    : [
                        node_information ,
                        node_charts,
                        node_log
                        
                        // node_children,
                        // node_log
                    ]
            }
    });
}
		
