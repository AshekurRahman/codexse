import React, { useContext } from "react";
import { Navigate, Route } from "react-router-dom";
import { UserContext } from "../UserContext";

// Component that redirects unauthenticated users to the login page
const PrivateRoute = ({ path, element }) => {
  const { userInfo } = useContext(UserContext);

  return userInfo ? (
    <Route path={path} element={element} />
  ) : (
    <Navigate to="/login" replace />
  );
};

// Component that redirects authenticated users to the home page
const PublicRoute = ({ path, element }) => {
  const { userInfo } = useContext(UserContext);

  return !userInfo ? (
    <Route path={path} element={element} />
  ) : (
    <Navigate to="/" replace />
  );
};

export { PrivateRoute, PublicRoute };
