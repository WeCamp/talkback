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
       /* return <div>
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
                            <td onClick={this.vote}>
                                {this.state.topic.vote_count} <i className="fa fa-thumbs-up"></i>
                            </td>
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
            <Comments source="/api/topics/"/>
        </div>*/

        return   <div><section id="banner">
            <header>
                <h2>{this.state.topic.title}</h2>
            </header>
            <p>By {this.state.topic.creator_name}</p>
            </section>
            <article className="container box style3">
            <header>
                <p className="TopicCreationDate">Creation date : {this.state.topic.created_at}</p>
                <p className="TopicVoteContainer" onClick={this.vote}>{this.state.topic.vote_count} <i className="fa fa-thumbs-up"></i></p>
            </header>
                <hr />
                {this.state.topic.details}
            </article>
            </div>
    }
});


var Comments = React.createClass({
    getInitialState: function(){
        return {
            data: {
                comments: []
            }
        };
    },
    componentDidMount: function() {
        var topicId = document.getElementById('showtopic').getAttribute('data-topicid');

        $.get(this.props.source + topicId, function(result) {
            if (this.isMounted()) {
                this.setState({data:
                    {
                        comments: result.comments
                    }
                });
            }
        }.bind(this));
    },
    render: function() {
        return <div>
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
    render: function() {
        return <div className="list-comments">
            {
                this.props.comments.map(function(comment) {
                    return <div className="row">
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

var showTopic = document.getElementById('showtopic');
React.render(
    <ShowTopic source="/api/topics/" />,
    showTopic
);
