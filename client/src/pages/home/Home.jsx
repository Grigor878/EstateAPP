import React, { useEffect } from "react";
import { useDispatch, useSelector } from "react-redux";
import { getAdminData, getTopHomes } from "../../store/slices/homeSlice";
import { setPaginatePage } from "../../store/slices/viewSlice";
import Main from "./components/main/Main";
import Searches from "./components/searches/Searches";
import Services from "./components/services/Services";
import Contracts from "./components/contracts/Contracts";
// import Contact from "../contact/Contact";
import PropType from "./components/propType/PropType";
import Faq from "./components/faq/Faq";
import HelmetAsync from "../../components/helmetAsync/HelmetAsync";
import Contacts from "../../components/contacts/Contacts";
// import "./Home.scss";

const Home = () => {
  const dispatch = useDispatch();
  const { language, sale, rent } = useSelector((state) => state.home);

  useEffect(() => {
    dispatch(getTopHomes(language));
    dispatch(getAdminData());
  }, [dispatch, language]);

  useEffect(() => {
    dispatch(setPaginatePage(1));
  }, [dispatch]);

  return (
    <section>
      <HelmetAsync
        description="main_title_seo"
      />
      <Main />
      <div className="contain">
        {/* <div className="home"> */}
        <div>
          <Searches />
          <PropType type="sale" data={sale} />
          <PropType type="rent" data={rent} />
          <Services />
          <div className="block">
            <Contracts />
          </div>
          <Faq />
        </div>
      </div>
      <Contacts />
    </section>
  );
};

export default Home;
