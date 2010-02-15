

Ext.namespace('tx','tx.caretaker');

tx.caretaker.NodeLog = Ext.extend( Ext.grid.GridPanel , {

    constructor: function(config) {
		
		this.json_data_store = new Ext.data.JsonStore({
			root: 'logItems',
			totalProperty: 'totalCount',
			idProperty: 'timestamp',
			remoteSort: false,
			fields: [
				'num',
				'title',
				{name: 'timestamp', mapping: 'timestamp', type: 'date', dateFormat: 'timestamp'},
				'stateinfo',
				'stateinfo_ll',
				'message',
				'message_ll',
				'state'
			],
			proxy: new Ext.data.HttpProxy({
				url: config.back_path + 'ajax.php?ajaxID=tx_caretaker::nodelog&node=' + config.node_id
			})
		});

		this.renderMessage = function(  value, metaData, record, rowIndex, colIndex, store ){

			var values = value.replace( /"/g , '&quot;' ).replace( /</g , '&lt;').replace( />/g , '&gt;').replace( /&/g , '&amp;' );

			var lines = values.split( "\n" );
			var title = lines[0];
			var message  = lines.splice( 1 ).join( '<br/>' );

			return '<div class="x-grid3-cell-inner" ext:qtitle="' + title  + '" ext:qtip="' +  message  + '" >' + title  + '</div>';
			
		}

		config = Ext.apply({

			collapsed        : true,
			collapsible      : true,
			stateful         : true,
			stateEvents      : ['expand','collapse'],
			stateId          : 'tx.caretaker.NodeLog',
			title            : 'Log',
			store            : this.json_data_store,
			trackMouseOver   : false,
			disableSelection : true,
			loadMask         : true,
			autoHeight       : true,

			// grid columns
			columns:[{
				header: "Time",
				dataIndex: 'timestamp',
				format: 'd.m.y H:i:s',
				fixed: true,
				width: 275
			},{
				header: "State",
				dataIndex: 'stateinfo_ll',
				fixed: true,
				width: 75
			},{
				header:'Message',
				dataIndex: 'message_ll',
				renderer:{ fn: this.renderMessage, scope: this }
			}],

			// customize view config
			viewConfig: {
				forceFit:true,
				enableRowBody:true,
				showPreview:true,
				getRowClass: function(record, index) {
					var state = parseInt( record.get('state') );
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
				store: this.json_data_store,
				displayInfo: true,
				displayMsg: 'Displaying topics {0} - {1} of {2}',
				emptyMsg: "No topics to display"
			})
		}, config);

		tx.caretaker.NodeLog.superclass.constructor.call(this, config);

		if (this.collapsed == false){
			this.json_data_store.load({params:{start:0, limit:10}});
		}

		this.on('expand', function(){
			this.json_data_store.load({params:{start:0, limit:10}});
		}, this);

	},

	getState: function() {
		var state = {
			collapsed: this.collapsed
		};
		return state;
	}

});

Ext.reg( 'caretaker-nodelog', tx.caretaker.NodeLog );