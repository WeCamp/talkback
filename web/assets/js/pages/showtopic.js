/** @jsx React.DOM */

var ShowTopic = React.createClass({
    getInitialState: function() {
        return {topic: {
            title: '',
            details: '',
            excerpt: '',
            creator: '',
            vote_count: 0,
            created_at: ''
        }};
    },
    componentDidMount: function() {
        console.log(this.refs);
        $.get(this.props.source, function(result) {
            if (this.isMounted()) {
                this.setState({topic: result});
            }
        }.bind(this));
    },
    render: function() {
        return <div className="panel panel-default">
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
    }
});

var showTopic = document.getElementById('showtopic')
React.render(
    <ShowTopic source="/api/topics/1"  />,
    showTopic
);
