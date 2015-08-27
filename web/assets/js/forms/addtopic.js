/** @jsx React.DOM */

var AddTopicForm = React.createClass({
    mixins: [formMethods],
    getInitialState: function() {
        return {
            errors: {},
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

        return <div>
            <div className="panel panel-default">
                <div className="panel-heading clearfix">
                    <h3 className="panel-title pull-left">Add topic</h3>
                </div>
                <div className="panel-body">
                    {submitted}
                    <div className="form-horizontal">
                        {this.renderTextInput('name', 'Name')}
                        {this.renderTextInput('excerpt', 'Excerpt')}
                        {this.renderTextarea('details', 'Details')}
                        {this.renderRadiosInline('owned_by_user', 'Do you want to claim the topic?', {
                            values: ['Yes', 'No'],
                            defaultCheckedValue: 'No'
                        })}
                    </div>
                </div>
                <div className="panel-footer">
                    <div className="row">
                        <div className="col-md-2">
                            <button type="button" className="btn btn-primary btn-block" onClick={this.handleSubmit}>Submit</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    },
    getFormData: function() {
        var data = {
            name: this.refs.name.getDOMNode().value,
            excerpt: this.refs.excerpt.getDOMNode().value,
            details: this.refs.details.getDOMNode().value,
            owned_by_user: this.refs.owned_by_userYes.getDOMNode().checked
        }
        return data
    },
    handleSubmit: function() {
        $.ajax({
            type: 'POST',
            url: 'URL',
            data: this.getFormData(),
            success: function(data) {
                window.location.href = '/';
            }.bind(this)
        });
    },
});

React.render(<AddTopicForm/>, document.getElementById('addtopicform'));
