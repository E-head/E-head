Ext.ns('PMS');

PMS.Menu = function(username, rolename) {
	username = username || '';
	rolename = rolename || '';
	return [{
	    xtype: 'box',
	    autoEl: {
	        tag: 'div',
	        style: 'cursor: pointer;',
	        qtip: 'e-head.ru',
	        cls: 'e-head-logo'
	    },
	    listeners: {
	        render: function(box) {
	            box.el.on('click', function() {
	                window.open('http://e-head.ru/');
	            })
	        }
	    }
	}, ' ', ' ', ' ', ' ', ' ', {
	    text: 'Архив заказов',
	    iconCls: 'archive-icon',
	    hidden: !acl.isView('archive'),
	    handler: function() {
	        PMS.System.Layout.getTabPanel().add({
	            iconCls: 'archive-icon',
	            xtype: 'PMS.Orders.Archive',
	            id: 'PMS.Orders.Archive'
	        });
	    }
	}, {
	    text: 'Контрагенты',
	    iconCls: 'customers-icon',
	    menu: [{
	        text: 'Заказчики',
	        iconCls: 'customers-icon',
	        hidden: !acl.isView('customers'),
	        handler: function() {
	            PMS.System.Layout.getTabPanel().add({
	                title: 'Заказчики',
	                iconCls: 'customers-icon',
	                entity: 'customers',
	                xtype: 'PMS.ContragentsListAbstract',
	                id: 'PMS.Customers.List'
	            });
	        }
	    }, {
	        text: 'Поставщики',
	        iconCls: 'suppliers-icon',
	        hidden: !acl.isView('suppliers'),
	        handler: function() {
	            PMS.System.Layout.getTabPanel().add({
	                title: 'Поставщики',
	                iconCls: 'suppliers-icon',
	                entity: 'suppliers',
	                xtype: 'PMS.ContragentsListAbstract',
	                id: 'PMS.Suppliers.List'
	            });
	        }
	    }]
	}, {
	    text: 'Отчёты',
	    iconCls: 'prod_schd-icon',
	    hidden: !acl.isView('orders'),
	    menu: [{
	        text: 'График производства',
	        iconCls: 'prod_schd-icon',
	        handler: function() {
	            window.open('/orders/report/schedule-production');
	        }
	    }, {
	        text: 'График монтажа',
	        iconCls: 'mount_schd-icon',
	        handler: function() {
	            window.open('/orders/report/schedule-mount');
	        }
	    }, {
	        text: 'План работ',
	        iconCls: 'work_schd-icon',
	        handler: function() {
	            window.open('/orders/report/planning');
	        }
	    }]
	}, {
		text: 'Менеджер доступа',
		iconCls: 'accounts_manager-icon',
		hidden: !acl.isView('admin'),
		handler: function() {
			PMS.System.Layout.getTabPanel().add({
				iconCls: 'accounts_manager-icon',
				xtype: 'xlib.acl.layout',
				id: 'xlib.acl.layout'
			});
		}
	}, '->', {
        text: 'Выход - <i>' + username + ' (' + rolename + ')</i>',
        iconCls: 'exit-icon',
        handler: function() {
            window.location.href = '/index/logout';
        }
    }];
}