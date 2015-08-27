/** @jsx React.DOM */

var BadgeList = React.createClass({
    getInitialState  : function () {
        return {
            badges: []
        };
    },
    componentDidMount: function () {
        $.get(this.props.source, function(result) {
            if (this.isMounted()) {
                this.setState({topics: result});
            }
        }.bind(this));
    },
    render           : function () {
        return <ul>
            {this.state.badges.map(function (badge, i) {
                return <BadgeRow badge={badge} key={i}/>;
            })}
        </ul>;
    }
});


var BadgeRow = React.createClass({
    render: function () {
        return <li>{this.props.badge.name}</li>;
    }
});

var target = document.getElementById('my-badges');

var apiSource = target.getAttribute('data-source');

React.render(<BadgeList source={apiSource}/>, target);