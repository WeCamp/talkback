/** @jsx React.DOM */

var BadgeList = React.createClass({
    getInitialState  : function () {
        return {
            badges: []
        };
    },
    componentDidMount: function () {
        var userId = reactCookie.load('userId');
        var url = '/api/users/'+userId+'/badges';
        $.get(url, function(result) {
            if (this.isMounted()) {
                this.setState({badges: result});
            }
        }.bind(this));
    },
    render: function () {
       /* return <div>
            <h1>My Badges</h1>
            <div className="row">
                {this.state.badges.map(function (badge, i) {
                    return <BadgeRow badge={badge} key={i}/>;
                })}
            </div>
        </div>;*/


        return <article className="container box style3">
            <div className="row">
                {this.state.badges.map(function (badge, i) {
                    return <BadgeRow badge={badge} key={i}/>;
                })}
            </div>
                    </article>;
    }
});


var BadgeRow = React.createClass({
    render: function () {
        return <div className="col-xs-12 col-md-4 col-lg-2 badgeEntry">
            <img src={"/assets/images/" + this.props.badge.icon} />
            <span>{this.props.badge.name}</span>
        </div>;
    }
});

var target = document.getElementById('my-badges');

var apiSource = target.getAttribute('data-source');

React.render(<BadgeList source={apiSource}/>, target);