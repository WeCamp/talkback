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

function ReactForm(Component) {
    return React.createClass({
        mixins: [formMethods],
        getInitialState: function() {
            return {
                submitted: null
            }
        },
        render: function() {
            var submitted
            if (this.state.submitted !== null) {
                submitted = <div className="alert alert-success">
                    <p>Much submit. wow</p>
                </div>
            }

            console.log(this.getInitialState());

            return <div>
                <div className="panel panel-default">
                    <div className="panel-heading clearfix">
                        <h3 className="panel-title pull-left">Add topic</h3>
                    </div>
                    <div className="panel-body">
                        <Component />
                    </div>
                    <div className="panel-footer">
                        <div className="row">
                            <div className="col-md-2">
                                <button type="button" className="btn btn-primary btn-block" onClick={this.handleSubmit}>Submit</button>
                            </div>
                        </div>
                    </div>
                </div>
                {submitted}
            </div>
        },
        isValid: function() {
            var fields = ['name', 'excerpt', 'details'],
                errors = {}
            fields.forEach(function(field) {
                var value = trim(this.refs[field].getDOMNode().value)
                if (!value) {
                    errors[field] = 'This field is required'
                }
            }.bind(this))
            this.setState({errors: errors})

            var isValid = true
            for (var error in errors) {
                isValid = false
                break
            }
            return isValid
        },
        handleSubmit: function() {
            if (this.isValid()) {
                this.setState({submitted: this.getFormData()})
            }
        },
    });
}

var AddTopic = React.createClass({
    mixins: [formMethods],
    getInitialState: function() {
        return {
            errors: {}
        }
    },
    render: function() {
        return <div className="form-horizontal">
            {this.renderTextInput('name', 'Name')}
            {this.renderTextInput('excerpt', 'Excerpt')}
            {this.renderTextarea('details', 'Details')}
            {this.renderRadiosInline('owned_by_user', 'Do you want to claim the topic?', {
                values: ['Yes', 'No'],
                defaultCheckedValue: 'No'
            })}
        </div>
    }
});

var AddTopicForm = ReactForm(AddTopic);
React.renderComponent(<AddTopicForm/>, document.getElementById('addtopicform'));


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
