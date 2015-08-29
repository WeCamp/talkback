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
        var submitted;
        if (this.state.submitted !== null) {
            submitted = <div className="alert alert-success">
                <p>Much submit. wow</p>
            </div>
        }

        return <div>
            <article className="container box style3 ProfilePage">
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
                            <button type="button" onClick={this.handleSubmit}>Submit</button>
            </article>
        </div>
    },
    getFormData: function() {
        var data = {
            title: this.refs.name.getDOMNode().value,
            excerpt: this.refs.excerpt.getDOMNode().value,
            details: this.refs.details.getDOMNode().value,
            owned_by_creator: this.refs.owned_by_userYes.getDOMNode().checked
        };
        return data;
    },
    handleSubmit: function() {
        var data = this.getFormData();
        $.ajax({
            type: 'POST',
            url: '/api/topics',
            data: data,
            beforeSend: function(xhr) {
                var userId = reactCookie.load('userId');
                xhr.setRequestHeader('X-UserId', userId);
            },
            success: function(data) {
                window.location.href = '/topic/'+data.id;
            },
            error: function(jqXHR, status, error) {
                console.log(status, jqXHR.responseJSON, error);
            }.bind(this)
        });
    }
});

React.render(<AddTopicForm/>, document.getElementById('addtopicform'));
