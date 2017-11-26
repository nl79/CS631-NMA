import React, { Component } from 'react';
import {connect} from 'react-redux';
import {bindActionCreators} from 'redux';

// Get the action function.
import {fetchWeather} from '../actions/index';

class SearchBar extends Component {
  constructor(props) {
    super(props);
    this.state = {
      term: ''
    }

    // NOTE: Explicitly bind the input handler to the Component's scope
    this.onInputChange = this.onInputChange.bind(this);
    this.onFormSubmit = this.onFormSubmit.bind(this);
  }
  onInputChange(event) {
    //console.log('onInputChange#event', event.target.value);
    this.setState({
      term: event.target.value
    });
  }

  onFormSubmit(event) {
    //console.log('onFormSubmit#event', event);
    event.preventDefault();

    // Get the fetch weather data.
    this.props.fetchWeather(this.state.term);
    this.setState({
      term: ''
    });
  }
  render() {
    return (
      <form onSubmit={this.onFormSubmit} className='input-group'>
        <input
            value={this.state.term}
            placeholder='Get a five day forecast in your favorite cities'
            className='form-control'
            onChange={this.onInputChange}
            />
        <span className='input-group-btn'>
          <button type='submit' className='btn btn-secondary'>Submit</button>
        </span>
      </form>
    );
  }
}
function mapDispatchToProps(dispatch) {
  return bindActionCreators({
    fetchWeather
  }, dispatch);
}

function mapStateToProps(state) {

}

export default connect(null, mapDispatchToProps)(SearchBar);
