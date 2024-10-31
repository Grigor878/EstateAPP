import React from "react";
import ReactDOM from "react-dom/client";
import "./index.scss";
import "../src/services/i18next/i18next";
import { Provider } from "react-redux";
import { PersistGate } from "redux-persist/integration/react";
import { BrowserRouter as Router } from "react-router-dom";
import store, { persistor } from "./store/store";
import View from "./view/View";

const root = ReactDOM.createRoot(document.getElementById("root"));
root.render(
  <Provider store={store}>
    <PersistGate loading={null} persistor={persistor}>
      <Router>
        <View />
      </Router>
    </PersistGate>
  </Provider>
);
