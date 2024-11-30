import React from "react";
import spinner from "../../assets/imgs/spiner.gif";

export const Loader = () => {
  return (
    <div className="loader">
      {/* <img src={loader} alt="Loading..." /> */}
      <img src={spinner} alt="Loading..." />
    </div>
  );
};
