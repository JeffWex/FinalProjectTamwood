import XMLReq from "./XMLReq.js";
let xmlReq = new XMLReq("http://localhost/Library-CMS/php/customer.php");
let cid = sessionStorage.getItem("cid");

class rBook {
    constructor(id, book_name, book_author, category) {
      this.id = id;
      this.book_name = book_name;
      this.book_author = book_author;

      const retBtn = document.createElement("button");
      retBtn.innerText = "Return";
      retBtn.type = "button";
      retBtn.classList.add("btn-accept");
      this.retBtn = retBtn;

      const retPress=(e)=>{
          let reqData = new FormData();   
          reqData.append("mode", "return-book");
          reqData.append("cid", cid);
          reqData.append("bid", this.id)
          xmlReq.Post(reqData).then(
            alert("Book returned!"),
            (rej)=>console.log(rej)
          )
          e.target.parentElement.parentElement.style.display="none";
        }
      retBtn.addEventListener("click", retPress);
    }

    toRow() {
        const tr = document.createElement("tr");
        for (let prop of Object.values(this)) {
          const td = document.createElement("td");
          (prop instanceof Object) ? td.append(prop) : td.innerText = prop;
          tr.append(td);
        }
        return tr;
      }
};
export default rBook;