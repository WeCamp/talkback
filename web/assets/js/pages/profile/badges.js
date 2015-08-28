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
                this.setState({badges: result});
            }
        }.bind(this));
    },
    render           : function () {
        return <div className="row">
            {this.state.badges.map(function (badge, i) {
                return <BadgeRow badge={badge} key={i}/>;
            })}
        </div>;
    }
});


var BadgeRow = React.createClass({
    render: function () {
        return <div className="col-xs-12 col-md-4 col-lg-2 badgeEntry">
            <img src={this.props.badge.icon} />
            <span>{this.props.badge.name}</span>
        </div>;
    }
});

var target = document.getElementById('my-badges');

var apiSource = target.getAttribute('data-source');

React.render(<BadgeList source={apiSource}/>, target);