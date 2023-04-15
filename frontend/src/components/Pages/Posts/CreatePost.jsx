import React, { useState } from "react";
import ReactQuill from "react-quill";
import "react-quill/dist/quill.snow.css";
import Button from "../../Button/Button";
import Form from "../../Form/Form";
import Input from "../../Input/Input";
import SiteHeader from "../../SiteHeader/SiteHeader";

const CreatePost = () => {
  const [value, setValue] = useState("");
  return (
    <>
      <SiteHeader title={`Create post`} />
      <div className="section-padding">
        <div className="container">
          <div className="row">
            <div className="col-lg-8 offset-lg-2">
              <Form>
                <Input placeholder="Title" />
                <Input type="file" />
                <ReactQuill value={value} onChange={setValue} />
                <Button type={`submit`}>Create post</Button>
              </Form>
            </div>
          </div>
        </div>
      </div>
    </>
  );
};

export default CreatePost;
