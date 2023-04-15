import React from "react";
import styles from "./Form.module.css";

const Form = ({ children, className, ...rest }) => {
  return (
    <form {...rest} className={`${className} ${styles.form_box}`}>
      {children}
    </form>
  );
};

export const Info = ({ children, className, ...rest }) => {
  return (
    <div {...rest} className={`${styles.form_info} ${className}`}>
      {children}
    </div>
  );
};

export const Divider = ({ children, className, ...rest }) => {
  return (
    <div {...rest} className={`${className} ${styles.devider}`}>
      {children}
    </div>
  );
};

export default Form;
