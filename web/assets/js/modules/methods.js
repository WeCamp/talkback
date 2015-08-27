/** @jsx React.DOM */

var formMethods = {
    renderTextInput: function(id, label) {
        return this.renderField(id, label,
            <input type="text" className="form-control" id={id} ref={id}/>
        )
    },
    renderTextarea: function(id, label) {
        return this.renderField(id, label,
            <textarea className="form-control" id={id} ref={id}/>
        )
    },
    renderRadiosInline: function(id, label, kwargs) {
        var radios = kwargs.values.map(function(value) {
            var defaultChecked = (value == kwargs.defaultCheckedValue)
            return <label className="radio-inline">
                <input type="radio" ref={id + value} name={id} value={value} defaultChecked={defaultChecked}/>
                {value}
            </label>
        })
        return this.renderField(id, label, radios)
    },
    renderField: function(id, label, field) {
        return <div className={$c('form-group', {'has-error': id in this.state.errors})}>
            <label htmlFor={id} className="col-sm-4 control-label">{label}</label>
            <div className="col-sm-6">
                {field}
            </div>
        </div>
    },
}

// Utils
var trim = function() {
    var TRIM_RE = /^\s+|\s+$/g
    return function trim(string) {
        return string.replace(TRIM_RE, '')
    }
}()

function $c(staticClassName, conditionalClassNames) {
    var classNames = []
    if (typeof conditionalClassNames == 'undefined') {
        conditionalClassNames = staticClassName
    }
    else {
        classNames.push(staticClassName)
    }
    for (var className in conditionalClassNames) {
        if (!!conditionalClassNames[className]) {
            classNames.push(className)
        }
    }
    return classNames.join(' ')
}
