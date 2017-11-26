import React from 'react';
import ReactDOM from 'react-dom';
import { Provider } from 'react-redux';
import { createStore, applyMiddleware } from 'redux';
import { ReduxLogger } from 'redux-logger';
import { Router, browserHistory, Route } from 'react-router';
import App from './Components/app';
import reducers from './reducers';
import routes from './routes';

/*
const createStoreWithMiddleware = applyMiddleware(
  ReduxLogger,
  ReduxPromise)(createStore);
*/

const store = createStore(reducers);

ReactDOM.render(

  <Provider store={store}>
    <Router history={ browserHistory } routes={routes} />
  </Provider>
  , document.querySelector('.app'));
