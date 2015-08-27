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
            <Comments source="/api/topics/"/>
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

        $.get(this.props.source + topicId, function (result) {
            if (this.isMounted()) {
                this.setState({
                    data: {
                        topicId : topicId,
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
                    <h3 className="panel-title pull-left">Give Feedback</h3>
                </div>
                <div className="panel-body">
                    <CommentForm topicId={this.state.topicId}/>
                </div>
            </div>
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

var CommentForm = React.createClass({
    mixins: [formMethods],
    render: function(){
        return <p>Comments form!</p>;
    }
});

var showTopic = document.getElementById('showtopic');
React.render(
    <ShowTopic source="/api/topics/" />,
    showTopic
);
