/** @jsx React.DOM */

var ChangeProfile = React.createClass({
    render: function() {
        return <div>
            <button type="button" className="btn btn-primary btn-block" onClick={this.changeProfile1}>Profile 1</button>
            <button type="button" className="btn btn-primary btn-block" onClick={this.changeProfile2}>Profile 2</button>
            <button type="button" className="btn btn-primary btn-block" onClick={this.changeProfile3}>Profile 3</button>
            </div>
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
    }
});

React.render(<ChangeProfile />, document.getElementById('change-profile'));