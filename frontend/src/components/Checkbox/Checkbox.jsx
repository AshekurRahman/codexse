import React from "react";
import styles from "./Checkbox.module.css";

const Checkbox = ({ children, label, id, ...props }) => {
  return (
    <div className={styles.Checkbox}>
      <input id={id} type="checkbox" {...props} />
      <label htmlFor={id}>{children ? children : label}</label>
    </div>
  );
};

export default Checkbox;
