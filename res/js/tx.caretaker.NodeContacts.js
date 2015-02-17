

Ext.namespace('tx','tx.caretaker');

tx.caretaker.NodeContacts = Ext.extend( Ext.grid.GridPanel , {

    constructor: function(config) {

		this.json_data_store = new Ext.data.JsonStore({
			root: 'contacts',
			totalProperty: 'totalCount',
			idProperty: 'id',
			remoteSort: false,
			fields: [
				'num','id',

				'node_title', 'node_type', 'node_type_ll', 'node_id',
				
				'role_id','role_name','role_description',

				'address_title','address_first_name', 'address_middle_name', 'address_last_name',
				'address_email','address_email_md5','address_phone','address_mobile',
				'address_company','address_address','address_city','address_zip','address_country'
			],
			proxy: new Ext.data.HttpProxy({
				url: TYPO3.settings.ajaxUrls['tx_caretaker::nodecontacts'] + '&node=' + config.node_id
			})
		});

		this.renderRole = function( value, metaData, record, rowIndex, colIndex, store ){
			var role = '';

			if ( record.data.role_name       )  role += ' ' + record.data.role_name ;

			return role;
		}

		this.renderNode = function( value, metaData, record, rowIndex, colIndex, store ){
			var nodeTitle = value;
			nodeTitle += ' [' + record.data.node_type + ']' ;
			return nodeTitle;
		}
		
		this.renderName  = function ( value, metaData, record, rowIndex, colIndex, store ){
			
			var full_name = '';

			if ( record.data.address_title       )  full_name += ' ' + record.data.address_title ;
			if ( record.data.address_first_name  )  full_name += ' ' + record.data.address_first_name ;
			if ( record.data.address_middle_name )  full_name += ' ' + record.data.address_middle_name ;
			if ( record.data.address_last_name   )  full_name += ' ' + record.data.address_last_name ;
				
			return full_name;
		}

		this.renderPhone = function(  value, metaData, record, rowIndex, colIndex, store ){

			return '<a href="tel:' + value + ' " >' + value  + '</a>';

		}

		this.renderMobile = function(  value, metaData, record, rowIndex, colIndex, store ){

			return '<a href="tel:' + value + ' " >' + value + '</a>';

		}

		this.renderMail = function(  value, metaData, record, rowIndex, colIndex, store ){

			return '<a href="mailto:' + value + ' " >' + value + '</a>';

		}

		this.renderPhoto = function(  value, metaData, record, rowIndex, colIndex, store ){
			var image = '';


			if ( record.data.address_email_md5 ){
				image =  '<img src="http://www.gravatar.com/avatar/' + record.data.address_email_md5 + '.jpg?s=40" />';
			}
			
			
			return image;
		}

		

		this.column_model = new Ext.grid.ColumnModel({
			defaults: {
				sortable: true
			},
			columns: [
			{
				header: "Photo",
				dataIndex: 'address_email',
				renderer:{ fn: this.renderPhoto, scope: this }
			},{
				header: "Role",
				dataIndex: 'role_name',
				renderer:{ fn: this.renderRole, scope: this }
			},{
				header: "Node",
				dataIndex: 'node_title',
				renderer:{ fn: this.renderNode, scope: this }
			},{
				header: 'Name',
				dataIndex: 'address_first_name',
				renderer:{ fn: this.renderName, scope: this }

			},{
				header: 'E-Mail',
				dataIndex: 'address_email',
				renderer:{ fn: this.renderMail, scope: this }
			},{
				header: 'Phone',
				dataIndex: 'address_phone',
				renderer:{ fn: this.renderPhone, scope: this }
			},{
				header: 'Mobile',
				dataIndex: 'address_mobile',
				renderer:{ fn: this.renderMobile, scope: this }
			}
			]
		});

		this.column_view = new Ext.grid.GridView({
			enableRowBody: true,
			showPreview: true,
			getRowClass: function(record, index) {
				return 'tx_caretaker_node_logrow tx_caretaker_node_logrow_OK';
			}
		});

		config = Ext.apply({
			iconCls: 'tx-caretaker-panel-nodecontacts',
			collapsed        : true,
			collapsible      : true,
			stateful         : true,
			stateEvents      : ['expand','collapse'],
			stateId          : 'tx.caretaker.NodeContacts',
			title            :'Contacts',
			titleCollapse    : true,
			store            : this.json_data_store,
			trackMouseOver   : false,
			disableSelection : true,
			loadMask         : true,
			autoHeight       : true,
			colModel         : this.column_model,
			view             : this.column_view

		}, config);

		tx.caretaker.NodeContacts.superclass.constructor.call(this, config);

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

Ext.reg( 'caretaker-nodecontacts', tx.caretaker.NodeContacts );