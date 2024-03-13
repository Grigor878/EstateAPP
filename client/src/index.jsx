import React from "react";
import ReactDOM from "react-dom/client";
import "./index.scss";
import "./i18next";
import { Provider } from "react-redux";
import { PersistGate } from "redux-persist/integration/react";
import store, { persistor } from "./store/store";
import View from "./view/View";

const root = ReactDOM.createRoot(document.getElementById("root"));
root.render(
  // <HelmetProvider>
    <Provider store={store}>
      <PersistGate loading={null} persistor={persistor}>
        <View />
      </PersistGate>
    </Provider>
  // </HelmetProvider>
);
