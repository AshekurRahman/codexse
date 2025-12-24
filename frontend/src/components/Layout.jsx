import React from "react";
import { UserContextProvider } from "../UserContext";
import Navbar from "./Navbar/Navbar";

const Layout = ({ children }) => {
  return (
    <UserContextProvider>
      <Navbar />
      <div className="layout-wrapper">{children}</div>
    </UserContextProvider>
  );
};

export default Layout;
