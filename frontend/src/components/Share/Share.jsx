import React from "react";
import {
  FaFacebookF,
  FaLink,
  FaLinkedinIn,
  FaPinterestP,
  FaTwitter,
} from "react-icons/fa";

import { SlShare } from "react-icons/sl";

import styles from "./Share.module.css";

const Share = ({ productUrl }) => {
  const handleCopyLink = () => {
    navigator.clipboard.writeText(productUrl);
  };

  return (
    <div className={styles.share_links}>
      <div className={styles.label}>
        <SlShare />
      </div>
      <div className={styles.icons}>
        <a
          href={`https://www.facebook.com/sharer.php?u=${productUrl}`}
          target="_blank"
          rel="noopener noreferrer"
          className={styles.icon}
        >
          <FaFacebookF />
        </a>
        <a
          href={`https://twitter.com/intent/tweet?url=${productUrl}`}
          target="_blank"
          rel="noopener noreferrer"
          className={styles.icon}
        >
          <FaTwitter />
        </a>
        <a
          href={`https://www.linkedin.com/sharing/share-offsite/?url=${productUrl}`}
          target="_blank"
          rel="noopener noreferrer"
          className={styles.icon}
        >
          <FaLinkedinIn />
        </a>
        <a
          href={`https://www.pinterest.com/pin/create/button/?url=${productUrl}`}
          target="_blank"
          rel="noopener noreferrer"
          className={styles.icon}
        >
          <FaPinterestP />
        </a>
        <button
          className={`${styles.icon} ${styles.copy}`}
          onClick={handleCopyLink}
        >
          <FaLink />
          <span>Copy link</span>
        </button>
      </div>
    </div>
  );
};

export default Share;
