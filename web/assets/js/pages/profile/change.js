/** @jsx React.DOM */

var ChangeProfile = React.createClass({
    render: function() {
        return <article className="container box style3 ProfilePage">
            <header>
            </header>
                <div onClick={this.changeProfile1} className="col-xs-12 col-md-4 col-lg-4 badgeEntry">
                    <img src="/assets/images/user.png" />
                    <span>Profile 1</span>
                </div>
                <div onClick={this.changeProfile2} className="col-xs-12 col-md-4 col-lg-4 badgeEntry">
                    <img src="/assets/images/user.png" />
                    <span>Profile 2</span>
                </div>
                <div  onClick={this.changeProfile3} className="col-xs-12 col-md-4 col-lg-4 badgeEntry">
                    <img src="/assets/images/user.png" />
                    <span>Profile 3</span>
                </div>

            <div className="UserSet">
                <span>User set successfully</span>
            </div>
        </article>;
    },
    changeProfile1: function() {
        this.changeProfile(1);
    },
    changeProfile2: function() {
        this.changeProfile(2);
    },
    changeProfile3: function() {
        this.changeProfile(3);
    },
    changeProfile: function(userId) {
        reactCookie.save('userId', userId, {path: '/', domain: ''});
        jQuery('.UserSet').fadeIn();
    }
});

React.render(<ChangeProfile />, document.getElementById('change-profile'));