import XMLReq from "../javascript/XMLReq.js";
let xmlReq = new XMLReq("http://localhost/php/LibrarySystem/BackEnd/customer.php");
let cid = sessionStorage.getItem("cid");

class book {
    constructor(id, book_name, book_author, category, quantity, price) {
      this.id = id;
      this.book_name = book_name;
      this.book_author = book_author;
      this.category = category;
      this.quantity = quantity;
      this.price = price;

      const selBtn = document.createElement("button");
      selBtn.innerText = "Select";
      selBtn.type = "button";
      selBtn.classList.add("btn-accept");
      this.selBtn = selBtn;

      const selPress=(e)=>{
          let reqData = new FormData();   
          reqData.append("mode", "select-book");
          reqData.append("bid",this.id);
          reqData.append("cid", cid);
          xmlReq.Post(reqData).then(
            alert("Book selected."),
            (rej)=>console.log(rej)
          )
          e.target.parentElement.parentElement.style.display="none";
        }
      selBtn.addEventListener("click", selPress);
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
export default book;