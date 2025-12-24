import React from "react";
import styles from "./Input.module.css";

const Input = ({ className, type, ...props }) => {
  return (
    <div className={styles.Input_group}>
      {type !== "textarea" ? (
        <input
          type={type}
          {...props}
          className={`${className} ${styles.Input_field}`}
        />
      ) : (
        <textarea {...props} className={`${className} ${styles.Input_field}`} />
      )}
    </div>
  );
};

export default Input;
