Ext.ns('PMS.Reports');

PMS.Reports.Staff = Ext.extend(xlib.form.FormPanel, {
    
    permissions: acl.isView('reports'),
    
    labelWidth: 60,
    
    defaults: {
        anchor: 0
    },
    
    initComponent: function() {
        
        this.periodStart = new xlib.form.DateField({
            format: xlib.date.DATE_FORMAT,
            hiddenFormat: xlib.date.DATE_FORMAT_SERVER,
            fieldLabel: 'Начало',
            allowBlank: false,
            value: new Date().add(Date.MONTH, -1).getFirstDateOfMonth(),
            name: 'start',
            hiddenName: 'start'
        });
        
        this.periodEnd = new xlib.form.DateField({
            format: xlib.date.DATE_FORMAT,
            hiddenFormat: xlib.date.DATE_FORMAT_SERVER,
            fieldLabel: 'Конец',
            allowBlank: false,
            value: new Date().add(Date.MONTH, -1).getLastDateOfMonth(),
            name: 'end',
            hiddenName: 'end'
        });
        
        this.items = [this.periodStart, this.periodEnd]
        
        PMS.Reports.Staff.superclass.initComponent.apply(this, arguments);
        
        var w = new Ext.Window({
            title: 'Отчёт о зарплате сотрудников за период:',
            resizable: false,
            width: 200,
            //height: 100,
            modal: true,
            items: [this],
            buttons: [{
                text: 'Сгенерировать',
                handler: acl.isView('reports') ? function() {
                    if (this.getForm().isValid()) {
                        window.open(link('staff', 'report', 'index', {
                            start: this.periodStart.getHiddenValue(), 
                            end: this.periodEnd.getHiddenValue()
                        }, 'html'));
                    }
                } : PMS.menuMessage,
                scope: this
            }, {
                text: 'Отмена',
                handler: function() {
                    w.close();
                },
                scope: this
            }]
        });
        
        w.show();
    }
});