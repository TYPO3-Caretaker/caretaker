

Ext.namespace('tx','tx.caretaker');

tx.caretaker.NodeProblems = Ext.extend( Ext.grid.GridPanel , {

    constructor: function(config) {
		
		this.json_data_store = new Ext.data.JsonStore({
			root: 'nodeProblems',
			totalProperty: 'totalCount',
			idProperty: 'node_id',
			remoteSort: false,
			fields: [
				'num',
				'title',
				'node_title',
				'node_id',
				'instance_title',
				'instance_id',
				{name: 'timestamp', mapping: 'timestamp', type: 'date', dateFormat: 'timestamp'},
				'stateinfo',
				'stateinfo_ll',
				'message',
				'message_ll',
				'state'
			],
			proxy: new Ext.data.HttpProxy({
				url: config.back_path + 'ajax.php?ajaxID=tx_caretaker::nodeproblems&node=' + config.node_id
			})
		});

		this.renderMessage = function(  value, metaData, record, rowIndex, colIndex, store ){

			var values = value.replace( /"/g , '&quot;' ).replace( /</g , '&lt;').replace( />/g , '&gt;').replace( /&/g , '&amp;' );

			console.debug(values);

			var lines = values.split( "\n" );
			var title = lines[0];
			var message  = lines.splice( 1 ).join( '<br/>' );

			console.debug(title);
			console.debug(message);
	console.debug("---");
			return '<div class="x-grid3-cell-inner" ext:qtitle="' + title  + '" ext:qtip="' +  message  + '" >' + title  + '</div>';

		}

		this.column_model = new Ext.grid.ColumnModel({
			defaults: {
				sortable: true
			},
			columns: [
			{
				header: "Title",
				dataIndex: 'node_title',
				width: 120
			},{
				header: "Instance",
				dataIndex: 'instance_title',
				width: 120
			},{
				header: "Time",
				dataIndex: 'timestamp',
				fixed: true,
				width: 110,
				xtype: 'datecolumn',
				format: 'd.m.y H:i:s'
			},{
				header: "State",
				dataIndex: 'stateinfo_ll',
				fixed: true,
				width: 50
			},{
				id: 'message',
				header: 'Message',
				dataIndex: 'message_ll',
				renderer:{ fn: this.renderMessage, scope: this }
			}]
		});

		this.column_view = new Ext.grid.GridView({
			enableRowBody: true,
			showPreview: true,
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
		});
			
		config = Ext.apply({
			autoExpandColumn : 'message',
			collapsed        : true,
			collapsible      : true,
			stateful         : true,
			stateEvents      : ['expand','collapse'],
			stateId          : 'tx.caretaker.NodeProblems',
			title            :'Problems',
			store            : this.json_data_store,
			trackMouseOver   : false,
			disableSelection : true,
			loadMask         : true,
			autoHeight       : true,
			colModel         : this.column_model,
			view             : this.column_view
			
		}, config);

		tx.caretaker.NodeProblems.superclass.constructor.call(this, config);

		if (this.collapsed == false){
			this.json_data_store.load();
		}
		
		this.on('expand', function(){
			this.json_data_store.load();
		}, this);


	},

	getState: function() {
		var state = {
			collapsed: this.collapsed
		};
		return state;
	}

});

Ext.reg( 'caretaker-nodeproblems', tx.caretaker.NodeProblems );