import React from "react";
import { AiOutlineHome } from "react-icons/ai";
import { BsArrowRight } from "react-icons/bs";
import { Link } from "react-router-dom";
import styles from "./SiteHeader.module.css";

const SiteHeader = ({ title, image }) => {
  return (
    <header
      className={styles.site_header}
      style={{ backgroundImage: `url(${image})` }}
    >
      <div className="container">
        <div className="row align-items-center">
          <div className="col-sm-6">
            <h1 className={styles.page_title}>{title}</h1>
          </div>
          <div className="col-sm-6">
            <ul className={styles.links}>
              <li>
                <Link exect="true" to="/">
                  <AiOutlineHome className="me-2" />
                  Home
                </Link>
              </li>
              {title && (
                <>
                  <li>
                    <BsArrowRight />
                  </li>
                  <li>{title}</li>
                </>
              )}
            </ul>
          </div>
        </div>
      </div>
    </header>
  );
};

export default SiteHeader;
