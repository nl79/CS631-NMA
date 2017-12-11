import React, { Component } from 'react';

export class SearchBar extends Component {
  constructor(props) {
    super(props);

    this.state = {
      query: ''
    };
  }

  onChange(e) {
    this.setState({query: e.target.value})
  }
  onSubmit() {
    if(this.props.onSubmit) {
      this.props.onSubmit(this.state.query);
    }
  }
  onKeyPress(e) {
    if (e.charCode == 13 || e.keyCode == 13) {
      this.onSubmit();
    }
  }
  render() {
    return (
      <div className='row'>
        <div className="col-lg-12">
          <div className="input-group">
            <input type="text" className="form-control"
                onChange={this.onChange.bind(this)}
                value={this.state.query} placeholder="Search for..."
                onKeyPress={this.onKeyPress.bind(this)}/>
            <span className="input-group-btn">
              <button className="btn btn-default"
                type="button"
                onClick={this.onSubmit.bind(this)}>Go!</button>
            </span>
          </div>
        </div>
      </div>
    );
  }
}
