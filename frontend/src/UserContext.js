import { createContext, useState } from "react";

export const UserContext = createContext({});

export function UserContextProvider({ children }) {
  const [userInfo, setUserInfo] = useState({});

  const contextValue = { userInfo, setUserInfo }; // create an object containing the userInfo and setUserInfo functions

  return (
    <UserContext.Provider value={contextValue}>{children}</UserContext.Provider>
  );
}
