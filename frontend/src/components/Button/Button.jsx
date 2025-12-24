import React from "react";
import styles from "./Button.module.css";

const Button = ({ children, type, className, ...props }) => {
  return (
    <button
      {...props}
      type={type ? type : `button`}
      className={`${className} ${styles.Button}`}
    >
      {children}
    </button>
  );
};

export default Button;
