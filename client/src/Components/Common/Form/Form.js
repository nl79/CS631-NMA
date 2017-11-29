import React, { Component } from 'react';

import { Field } from './Field';

export class Form extends Component {

  constructor(props) {
    super(props);

    let state = {};

    if(Array.isArray(props.fields)) {
      for(let i = 0; i < props.fields.length; ++i) {

        state[props.fields[i].name] = props.fields[i].default || '';

        // Check if data was pre-supplied to the form.
        if(props.data && props.data[props.fields[i].name]) {
          state[props.fields[i].name] = props.data[props.fields[i].name] || '';
        }
      }
    }
    this.state = state;
  }

  componentWillMount() { }

  componentWillReceiveProps(props) {
    if(props.data) {
      this.setState( {...props.data} );
    }
  }

  onChange(field, value) {
    this.setState({ ...this.state, [field.name]: value },
    (e)=> {
      if(this.props.onChange) {
        this.props.onChange({...this.state});
      }
    });
  }

  onSubmit() {

    if(this.props.onSubmit) {
      this.props.onSubmit({...this.state});
    }
  }

  getData() { }

  render() {
    return (
      <form className={this.props.className || ''}>
        <h4>{this.props.title || '' }</h4>
        {
          this.props.fields.map((o, i) => {
            if(o.type === 'hidden') { return; }
            return (<div key={i} className="form-group">
              <label className='control-label'>{o.label}</label>
              <Field
                className="form-control"
                value={this.state[o.name]}
                onChange={(v)=>{ this.onChange(o, v) }}
                config={o} />

            </div>);
          })
        }
        {
          this.props.onSubmit
            ? <button type="button" onClick={this.onSubmit.bind(this)} className="btn btn-primary">Submit</button>
            : null
        }
      </form>
    );
  }
}
