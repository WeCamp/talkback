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

    vote: function(event) {
        event.stopPropagation();

        var url = '/api/topics/' + this.state.topic.id + '/vote';
        var data = {
            topic: this.state.topic.id
        };

        var self = this;

        $.ajax({
            type: 'POST',
            url: url,
            data: data,
            beforeSend: function(xhr) {
                var userId = reactCookie.load('userId');
                xhr.setRequestHeader('X-UserId', userId);
            },
            success: function(data) {
                var topic = self.state.topic;
                topic.vote_count = parseInt(topic.vote_count) + 1;
                self.setState({topic: topic});
            },
            error: function(jqXHR, status, error) {
                console.log(status, jqXHR.responseJSON, error);
            }.bind(this)
        });
    },

    render: function() {

        document.getElementById('header_title').innerHTML = this.state.topic.title;
        document.getElementById('header_underline').innerHTML = this.state.topic.creator_name;

        return <div>
            <article className="container box style3">
                <header>
                    <p className="TopicCreationDate">Creation date : {this.state.topic.created_at}</p>

                    <p className="TopicVoteContainer" onClick={this.vote}>{this.state.topic.vote_count} <i
                        className="fa fa-thumbs-up"></i></p>
                </header>
                <hr />
                {this.state.topic.details}
            </article>

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

                <article className="container box style3">
                    <CommentList comments={this.state.data.comments}/>
                </article>
        </div>
    }
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
    }
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
        return <article className="container box style3">
            <div className="panel-heading clearfix">
                <h3 className="panel-title pull-left">Give Feedback</h3>
            </div>
            <div className="panel-body">
                <div className="form-horizontal">
                    {this.renderHiddenInput('topic_id', this.props.topicId)}
                    {this.renderTextarea('content')}
                </div>
            </div>
                        <button type="button" onClick={this.handleSubmit}>
                            Submit
                        </button>
        </article>;
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
            beforeSend: function(xhr) {
                var userId = reactCookie.load('userId');
                xhr.setRequestHeader('X-UserId', userId);
            },
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
