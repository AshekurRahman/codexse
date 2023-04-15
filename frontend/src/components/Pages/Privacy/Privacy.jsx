import React from "react";
import SiteHeader from "../../SiteHeader/SiteHeader";

const Privacy = () => {
  return (
    <>
      <SiteHeader title={`Privacy policy`} />
      <div className="section-padding">
        <div className="container">
          <p>
            At Codexse, we understand the importance of your privacy and we are
            committed to protecting it. This Privacy Policy explains what
            information we collect from you when you use our website, how we use
            it, and how we protect it.
          </p>
          <br />
          <h5>Information we collect</h5>
          <p>
            We may collect personal information from you when you interact with
            our website, including when you register for an account, make a
            purchase, subscribe to our newsletter, or contact us for support.
            The personal information we collect may include your name, email
            address, billing address, and payment information.
          </p>
          <p>
            In addition, we may collect non-personal information when you use
            our website, including your IP address, browser type, operating
            system, and the pages you visit on our site.
          </p>
          <br />
          <h5>How we use your information</h5>
          <p>
            We use your personal information to provide you with the products
            and services you request, including processing your orders and
            providing customer support. We may also use your information to send
            you marketing communications about our products and services, but
            you can opt-out of receiving these communications at any time.
          </p>
          <p>
            We may use non-personal information to improve our website and to
            analyze trends and usage patterns.
          </p>
          <br />
          <h5>How we protect your information</h5>
          <p>
            We take reasonable measures to protect your personal information
            from unauthorized access, use, or disclosure. We use
            industry-standard security measures such as encryption, firewalls,
            and secure servers to protect your information.
          </p>
          <p>
            However, please note that no data transmission over the internet or
            electronic storage is completely secure. While we take reasonable
            measures to protect your information, we cannot guarantee its
            absolute security.
          </p>
          <br />
          <h5>Sharing your information</h5>
          <p>
            We do not sell, trade, or rent your personal information to third
            parties. However, we may share your information with third-party
            service providers who help us operate our website or provide
            services to you, such as payment processors or shipping companies.
          </p>
          <p>
            We may also share your information when required by law or to
            protect our legal rights.
          </p>
          <br />
          <h5>Cookies</h5>
          <p>
            We use cookies on our website to enhance your user experience and to
            collect non-personal information. A cookie is a small file that is
            placed on your device when you visit our website. You can set your
            browser to refuse cookies or to alert you when cookies are being
            sent, but please note that some parts of our website may not
            function properly if you disable cookies.
          </p>
          <br />
          <h5>Changes to this Privacy Policy</h5>
          <p>
            We may update this Privacy Policy from time to time. If we make any
            material changes, we will notify you by email or by posting a notice
            on our website prior to the change becoming effective. We encourage
            you to review this Privacy Policy periodically to stay informed
            about our practices.
          </p>
          <h5>Contact Us</h5>
          <p>
            If you have any questions or concerns about this Privacy Policy,
            please contact us at{" "}
            <a href="mailto:ashekurrahman1@gmail.com">
              ashekurrahman1@gmail.com
            </a>
            .
          </p>
        </div>
      </div>
    </>
  );
};

export default Privacy;
