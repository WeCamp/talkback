/** @jsx React.DOM */

var TopicRow = React.createClass({
    render: function() {
        console.log(this.props.topic);
        return <tr onClick={this.handleClick}>
            <td>{this.props.topic.creator_name}</td>
            <td>{this.props.topic.title}</td>
            <td>{this.props.topic.excerpt}</td>
            <td>{this.props.topic.created_at}</td>
            <td>{this.props.topic.vote_count}</td>
        </tr>
    },
    handleClick: function() {
        window.location.href = '/topic/' + this.props.topic.id;
    }
});

var TopicList = React.createClass({

    getInitialState: function() {
        return {
            topics: []
        };
    },

    componentDidMount: function() {
        $.get(this.props.source, function(result) {
            if (this.isMounted()) {
                this.setState({topics: result});
            }
        }.bind(this));
    },

    render: function() {
        console.log(this.state);
        return <div className="panel panel-default">
            <div className="panel-heading clearfix">
                <h3 className="panel-title pull-left">Topics</h3>
            </div>
            <div className="panel-body">
                <table className="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Creator</th>
                            <th>Topic</th>
                            <th>Excerpt</th>
                            <th>Created</th>
                            <th>Votes</th>
                        </tr>
                    </thead>
                    <tbody>
                    {this.state.topics.map(function(topic, i) {
                        return <TopicRow topic={topic} key={i} />;
                    })}
                    </tbody>
                </table>
            </div>
        </div>
    }
});

React.render(
    <TopicList source="/api/topics" />,
    document.getElementById('topiclist')
);