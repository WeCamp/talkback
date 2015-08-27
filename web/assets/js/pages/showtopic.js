/** @jsx React.DOM */

var ShowTopic = React.createClass({
    getInitialState: function() {
        return {
            topic: {
                id: 0,
                title: '',
                details: '',
                excerpt: '',
                creator: '',
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
                    <div>This topic was added by {this.state.topic.creator} at {this.state.topic.created_at}</div>
                    <div>{this.state.topic.details}</div>
                    <div>{this.state.topic.excerpt}</div>
                    <div>Votes: {this.state.topic.vote_count}</div>
                </div>
            </div>
            <Comments />
        </div>
    }
});


var Comments = React.createClass({
    getInitialState: function(){
        return {
            data: {
                comments: [
                    {
                        'username': 'Bob',
                        'content': 'This sucks!'
                    },
                    {
                        'username': 'Shaniqua',
                        'content': 'Duuussss'
                    },
                    {
                        'username': 'Adri',
                        'content': 'WOW!'
                    },
                ]
            }
        };
    },
    render: function() {
        return <div>
            <div className="panel panel-default">
                <div className="panel-heading clearfix">
                    <h3 className="panel-title pull-left">Comments</h3>
                </div>
                <div className="panel-body">
                    <CommentList data={this.state.data}/>
                </div>
            </div>
        </div>
    },
});

var CommentList = React.createClass({
    render: function() {
        return <table className="table table-striped">
            <thead>
                <tr>
                    <th width="25%">User</th>
                    <th>Content</th>
                </tr>
            </thead>
            {
                this.props.data.comments.map(function(comment) {
                    return <tr>
                        <td>{comment.username}</td>
                        <td>{comment.content}</td>
                    </tr>
                })
            }
        </table>
    },
});

var showTopic = document.getElementById('showtopic');
React.render(
    <ShowTopic source="/api/topics/"  />,
    showTopic
);
