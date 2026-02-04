import $ from "jquery";

const originalState = {
    status: state.status,
    year: state.year,
    search: state.search
};

$(`.prose`).on('change', function (){
    changeContent = $(this).getContent();
    console.log(originalContent);
    console.log(changeContent);
    if(originalContent !== changeContent){
        console.log("Not Equal")
        console.log(originalContent);
        console.log(changeContent);
    }
});
