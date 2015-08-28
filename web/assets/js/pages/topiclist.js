/** @jsx React.DOM */

var TopicRow = React.createClass({
    getInitialState: function() {
        return this.props;
    },
    render: function() {
      /*  return <tr onClick={this.handleClick}>
            <td>{this.state.topic.creator_name}</td>
            <td>{this.state.topic.title}</td>
            <td>{this.state.topic.excerpt}</td>
            <td>{this.state.topic.created_at}</td>
            <td onClick={this.vote}>
                {this.state.topic.vote_count} < i className="fa fa-thumbs-up"></i>
            </td>
        </tr>*/

        return <article id="first" className="container box style1 right">
            <div class="inner">
                <header>
                    <h2>{this.state.topic.title}</h2><h4>- {this.state.topic.creator_name}</h4>
                </header>
                <p>{this.state.topic.excerpt}</p>
                <p className="voteContainer" onClick={this.vote}> {this.state.topic.vote_count} < i className="fa fa-thumbs-up"></i></p>
                <p className="created_at">Creation date : {this.state.topic.created_at}</p>
                <p className="readMore" onClick={this.handleClick}>Read More...</p>
            </div>
        </article>
    },
    handleClick: function(event) {
        window.location.href = '/topic/' + this.props.topic.id;
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
        return <div className="">
                    {this.state.topics.map(function(topic, i) {
                        return <TopicRow topic={topic} key={i} />;
                    })}
        </div>
    }
});

React.render(
    <TopicList source="/api/topics" />,
    document.getElementById('topiclist')
);