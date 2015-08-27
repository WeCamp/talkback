/** @jsx React.DOM */

var ShowTopic = React.createClass({
    getInitialState: function() {
        return {
            topic: {
                id: 0,
                title: '',
                details: '',
                excerpt: '',
                creator_name: '',
                vote_count: 0,
                created_at: ''
            }
        };
    },

    componentDidMount: function() {
        var showTopic = document.getElementById('showtopic');
        var topicId = showTopic.getAttribute('data-topicid');

        $.get(this.props.source + topicId, function(result) {
            if (this.isMounted()) {
                this.setState({topic: result});
            }
        }.bind(this));
    },

    render: function() {
        return <div>
            <div className="panel panel-default">
                <div className="panel-heading clearfix">
                    <h3 className="panel-title pull-left">{this.state.topic.title}</h3>
                </div>
                <div className="panel-body">
                    <table className="table table-striped table-topics">
                        <tr>
                            <td>Created by</td>
                            <td>{this.state.topic.creator_name}</td>
                        </tr>
                        <tr>
                            <td>Created at</td>
                            <td>{this.state.topic.created_at}</td>
                        </tr>
                        <tr>
                            <td>Upvotes</td>
                            <td>{this.state.topic.vote_count}</td>
                        </tr>
                    </table>
                    <h4>My great idea is about:</h4>
                    <div className="row">
                        <div className="col-md-12">
                            {this.state.topic.details}
                        </div>
                    </div>
                </div>
            </div>
            <Comments topic={this.state.topic}/>
        </div>
    }
});


var Comments = React.createClass({

    getInitialState: function(){
        return {
            topic: undefined,
            data: {
                comments: []
            }
        };
    },

    componentWillReceiveProps: function(props) {
        if (this.isMounted()) {
            this.setState({
                topic: props.topic
            }, function(){
                this.updateComments()
            });
        }
    },

    componentDidMount: function() {
        this.updateComments();
    },

    updateComments: function(){

        if (undefined === this.state.topic || undefined === this.state.topic.id) {
            // Not updating comments, as no topic set
            return;
        }

        var topicId = this.props.topic.id;

        $.get('/api/topics/' + topicId, function (result) {
            var comments = result.comments;

            if (this.isMounted()) {

                // Sort by date DESC
                comments = comments.sort(function(a, b){
                    return (a.created_at > b.created_at) ? -1 : 1;
                });

                this.setState({
                    data: {
                        comments: comments
                    }
                });
            }
        }.bind(this));
    },

    render: function() {

        return <div>

            <CommentForm topic={this.state.topic} parent={this}/>

            <div className="panel panel-default">
                <div className="panel-heading clearfix">
                    <h3 className="panel-title pull-left">Comments</h3>
                </div>
                <div className="panel-body">
                    <CommentList comments={this.state.data.comments}/>
                </div>
            </div>
        </div>
    },
});

var CommentList = React.createClass({

    getInitialState: function(){
        return {
            data: {
                comments: []
            }
        };
    },

    componentWillReceiveProps: function(nextProps) {
        var state = this.state;

        state.data.comments = nextProps.comments;
        this.setState(state);
    },

    render: function() {
        return <div className="list-comments">
            {
                this.state.data.comments.map(function(comment, i) {
                    return <div className="row" key={comment.id}>
                        <div className="col-md-12">
                            <strong>{comment.name}:</strong><br />
                            {comment.content}
                        </div>
                    </div>
                })
            }
        </div>
    },
});

var CommentForm = React.createClass({

    mixins: [formMethods],

    getInitialState: function() {
        return {
            errors: {},
            submitted: null
        }
    },

    render: function(){
        return <div className="panel panel-default">
            <div className="panel-heading clearfix">
                <h3 className="panel-title pull-left">Give Feedback</h3>
            </div>
            <div className="panel-body">
                <div className="form-horizontal">
                    {this.renderHiddenInput('topic_id', this.props.topicId)}
                    {this.renderTextarea('content', 'Your feedback')}
                </div>
            </div>
            <div className="panel-footer">
                <div className="row">
                    <div className="col-md-2">
                        <button type="button" className="btn btn-primary btn-block" onClick={this.handleSubmit}>
                            Submit
                        </button>
                    </div>
                </div>
            </div>
        </div>;
    },

    getFormData: function() {
        return {
            content: this.refs.content.getDOMNode().value
        };
    },

    handleSubmit: function() {

        var data = this.getFormData();

        $.ajax({
            type: 'POST',
            url: comment_new_url,
            data: data,
            success: function(data) {

                if (undefined == this.props.parent) {
                    return;
                }

                // Clear form
                this.refs.content.getDOMNode().value = '';

                // Reload comments
                this.props.parent.updateComments();

            }.bind(this),
            error: function(jqXHR, status, error) {
                console.log(status, jqXHR.responseJSON, error);
            }.bind(this)
        });
    }
});

var showTopic = document.getElementById('showtopic');

React.render(
    <ShowTopic source="/api/topics/" />,
    showTopic
);
