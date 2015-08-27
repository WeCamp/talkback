/** @jsx React.DOM */

function ReactForm(Component) {
    return React.createClass({
        mixins: [formMethods],
        getInitialState: function() {
            return {
                submitted: null
            }
        },
        render: function() {
            var submitted
            if (this.state.submitted !== null) {
                submitted = <div className="alert alert-success">
                    <p>Much submit. wow</p>
                </div>
            }

            return <div>
                <div className="panel panel-default">
                    <div className="panel-heading clearfix">
                        <h3 className="panel-title pull-left">Add topic</h3>
                    </div>
                    <div className="panel-body">
                        {submitted}
                        <Component />
                    </div>
                    <div className="panel-footer">
                        <div className="row">
                            <div className="col-md-2">
                                <button type="button" className="btn btn-primary btn-block" onClick={this.handleSubmit}>Submit</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        },
        handleSubmit: function(lorem) {
            console.log(lorem);
            this.setState(
                {
                    submitted: this.getFormData()
                }
            )
            this.submitFormData()
        },
        getFormData: function() {
            return this.props.getFormData
        },
        submitFormData: function() {
            return this.props.submitFormData
        },
    });
}
