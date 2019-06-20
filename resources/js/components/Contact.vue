<template>
  <section id="contact">
    <h1 class="section-header">CONTACT</h1>

    <div class="contact-wrapper">
      <!---------------- 

        CONTACT PAGE LEFT 
        
      ----------------->

      <form @submit="$event.preventDefault()" class="form-horizontal" role="form">
        <div class="form-group">
          <div class="col-sm-12">
            <input
              type="text"
              class="form-control"
              id="name"
              placeholder="NOME"
              name="name"
              required
              v-model="form.name"
            >
          </div>
        </div>

        <div class="form-group">
          <div class="col-sm-12">
            <input
              type="email"
              class="form-control"
              id="email"
              placeholder="EMAIL"
              v-model="form.email"
              required
            >
          </div>
        </div>

        <div class="form-group">
          <div class="col-sm-12">
            <!-- <the-mask class="form-control" mask="['(##) ####-####', '(##) #####-####']" v-model="form.phone" type="text" masked="false" placeholder="TELEFONE" required></the-mask> -->

            <input
              type="tel"
              class="form-control"
              id="phone"
              placeholder="TELEFONE"
              v-model="form.phone"
              v-mask="['(##) ####-####', '(##) #####-####']"
              masked="false"
              required
            >
          </div>
        </div>

        <div class="input-group" style="margin-bottom: 15px;">
          <div class="input-group-prepend">
            <span class="input-group-text" id="inputGroupFileAddon01">Upload</span>
          </div>
          <div class="custom-file form-group upload-btn">
            <input
              type="file"
              class="custom-file-input"
              id="inputGroupFile01"
              aria-describedby="inputGroupFileAddon01"
              @change="getAttachedFile($event)"
              name="attached_file"
            >
            <label
              v-if="!form.filename"
              style="background-color: #111"
              class="custom-file-label"
              for="inputGroupFile01"
            >Tipo pdf, doc, docx, odt ou txt|500 kb</label>

            <label
              v-else
              style="background-color: #111"
              class="custom-file-label"
              for="inputGroupFile01"
            >{{ form.filename }}</label>
          </div>
        </div>

        <input type="hidden" v-model="form.sended_at" name="sended_at">

        <textarea
          class="form-control"
          v-model="form.message"
          rows="10"
          placeholder="MENSAGEM"
          name="message"
          required
        ></textarea>

        <button
          @click="sendMessage"
          class="btn btn-primary send-button"
          id="submit"
          type="submit"
          value="SEND"
        >
          <div class="button">
            <i class="fa fa-paper-plane"></i>
            <span class="send-text">SEND</span>
          </div>
        </button>
      </form>

      <!---------------- 

        CONTACT PAGE RIGHT 
        
      ----------------->

      <div class="direct-contact-container">
        <ul class="contact-list">
          <li class="list-item">
            <i class="fa fa-map-marker fa-2x">
              <span class="contact-text place">Seattle | WA</span>
            </i>
          </li>

          <li class="list-item">
            <i class="fa fa-phone fa-2x">
              <span class="contact-text phone">
                <a href="tel:1-212-555-5555" title="Give me a call">(212) 555-2368</a>
              </span>
            </i>
          </li>

          <li class="list-item">
            <i class="fa fa-envelope fa-2x">
              <span class="contact-text gmail">
                <a href="mailto:#" title="Send me an email">company@gmail.com</a>
              </span>
            </i>
          </li>
        </ul>

        <div class="copyright">&copy; ALL OF THE RIGHTS RESERVED</div>
      </div>
    </div>
  </section>
</template>

<script>
import { mask } from "vue-the-mask";
import axios, { post } from "axios";

export default {
  directives: { mask },
  data() {
    return {
      error: false,
      allowedMimes: [
        "application/vnd.oasis.opendocument.text", // odt
        "text/plain", // txt
        "application/msword",
        "application/pdf"
      ],
      allowedExt: ["doc", "docx"],
      form: {
        name: "",
        phone: "",
        email: "",
        message: "",
        attached_file: null,
        filename: ""
      }
    };
  },
  methods: {
    getAttachedFile(event) {
      const file = event.target.files[0];
      const fileExt = file.name.split("/")[0];
      const fileType = file.type;
      // Checando Ext / MIME Type
      if (this.allowedMimes.includes(fileType)) {
        this.form.attached_file = file;
      } else if (this.allowedExt.includes(fileExt)) {
        this.form.attached_file = file;
      } else {
        this.error = true;
        alert("Tipo de arquivo não permitido");
      }

      // Checando Size
      if (file.size > 500000) {
        this.error = true;
        alert("Arquivo muito grande. Máximo aceito é de 500kb");
      }

      this.form.filename = file.name;
      console.log(this.form);
    },
    removeWhiteSpaces(maskValue) {
      return maskValue.replace(" ", "");
    },
    async sendMessage(event) {
      const formData = new FormData();
      const url = "https://desafio-netshow.herokuapp.com/api/contact/send";
      formData.append("name", this.form.name);
      formData.append("email", this.form.email);
      formData.append("phone", this.removeWhiteSpaces(this.form.phone));
      formData.append("message", this.form.message);
      if (this.form.attached_file) {
        formData.append("attached_file", this.form.attached_file);
      } else {
        alert("Envie um arquivo!");
      }
      const config = {
        headers: {
          "Content-Type": "multipart/form-data",
          "X-CSRF-TOKEN": window.Laravel.csrfToken
        }
      };

      if (!this.error) {
        const response = await post(url, formData, config);
        console.log(response.data);
      }
    }
  }
};
</script>


<style scoped>
.col-sm-12 {
  padding: 0px !important;
}

body {
  margin: 0;
  padding: 0;
}

#contact {
  width: 100%;
  height: 100vh;
  background-color: #111;
  overflow: hidden;
  padding-bottom: 200px;
}

.section-header {
  text-align: center;
  margin: 0 auto;
  padding-top: 50px;
  margin-bottom: 50px;
  font: 300 60px "Oswald", sans-serif;
  letter-spacing: 6px;
  color: #fff;
  margin-bottom: 25px;
}

.contact-wrapper {
  margin: 0 auto;
  padding-top: 20px;
  position: relative;
  max-width: 800px;
}

/* Begin Left Contact Page */
.form-horizontal {
  float: left;
  max-width: 400px;
  font-family: "Lato";
  font-weight: 400;
}

.form-control,
textarea {
  max-width: 400px;
  background-color: #111;
  color: #fff;
  letter-spacing: 1px;
}

.send-button {
  margin-top: 15px;
  height: 45px;
  width: 400px;
  overflow: hidden;
  transition: all 0.2s ease-in-out;
}

.button {
  width: 400px;
  height: 34px;
  transition: all 0.2s ease-in-out;
}

.send-text {
  display: block;
  margin-top: 10px;
  font: 300 14px "Lato", sans-serif;
  letter-spacing: 2px;
}

.button:hover {
  transform: translate3d(0px, -29px, 0px);
}

/* Begin Right Contact Page */
.direct-contact-container {
  max-width: 400px;
  float: right;
  margin-top: 5px;
  text-align: right;
}

/* Location, Phone, Email Section */
.contact-list {
  list-style-type: none;
  margin-left: -30px;
  padding-right: 20px;
}

.list-item {
  line-height: 4;
  color: #aaa;
}

.contact-text {
  font: 300 18px "Lato", sans-serif;
  letter-spacing: 1.9px;
  color: #bbb;
}

.place {
  margin-left: 62px;
}

.phone {
  margin-left: 56px;
}

.gmail {
  margin-left: 53px;
}

.contact-text a {
  color: #bbb;
  text-decoration: none;
  transition-duration: 0.2s;
}

.contact-text a:hover {
  color: #fff;
  text-decoration: none;
}

/* Social Media Icons */
.social-media-list {
  position: relative;
  font-size: 2.3rem;
  text-align: center;
  width: 100%;
}

.social-media-list li a {
  color: #fff;
}

.social-media-list li {
  position: relative;
  top: 0;
  left: -20px;
  display: inline-block;
  height: 70px;
  width: 70px;
  margin: 10px auto;
  line-height: 70px;
  border-radius: 50%;
  color: #fff;
  background-color: rgb(27, 27, 27);
  cursor: pointer;
  transition: all 0.2s ease-in-out;
}

.social-media-list li:after {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  width: 70px;
  height: 70px;
  line-height: 70px;
  border-radius: 50%;
  opacity: 0;
  box-shadow: 0 0 0 1px #fff;
  transition: all 0.2s ease-in-out;
}

.social-media-list li:hover {
  background-color: #fff;
}

.social-media-list li:hover:after {
  opacity: 1;
  transform: scale(1.12);
  transition-timing-function: cubic-bezier(0.37, 0.74, 0.15, 1.65);
}

.social-media-list li:hover a {
  color: #111;
}

.copyright {
  font: 200 14px "Oswald", sans-serif;
  color: #555;
  letter-spacing: 1px;
  text-align: center;
}

hr {
  border-color: rgba(255, 255, 255, 0.8);
}

/* Begin Media Queries*/
@media screen and (max-width: 760px) {
  #contact {
    height: 1000px;
  }
  .section-header {
    font-size: 65px;
  }
  .direct-contact-container,
  .form-horizontal {
    float: none;
    margin: 10px auto;
  }
  .direct-contact-container {
    margin-top: 60px;
    max-width: 300px;
  }
  .social-media-list li {
    height: 60px;
    width: 60px;
    line-height: 60px;
  }
  .social-media-list li:after {
    width: 60px;
    height: 60px;
    line-height: 60px;
  }
}

@media screen and (max-width: 569px) {
  #contact {
    height: 1200px;
  }
  .section-header {
    font-size: 50px;
  }
  .direct-contact-container,
  .form-wrapper {
    float: none;
    margin: 0 auto;
  }
  .form-control,
  textarea {
    max-width: 340px;
    margin: 0 auto;
  }

  .name,
  .email,
  textarea {
    width: 280px;
  }
  .direct-contact-container {
    margin-top: 60px;
    max-width: 280px;
  }
  .social-media-list {
    left: 0;
  }
  .social-media-list li {
    height: 55px;
    width: 55px;
    line-height: 55px;
    font-size: 2rem;
  }
  .social-media-list li:after {
    width: 55px;
    height: 55px;
    line-height: 55px;
  }
}

@media screen and (max-width: 410px) {
  .send-button {
    width: 99%;
  }
}
</style>
