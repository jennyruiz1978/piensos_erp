
class DB{

    
    constructor(API, verbo){
        this.API=API;
        this.verbo=verbo;
    }
    
    async get(params){

        const settings = {
            method: this.verbo,
            headers: {
              'Accept': 'application/json',
              'Content-Type': 'application/json',
            },
            body: JSON.stringify(params)
        }

        let res = await fetch(this.API, settings);
        return res.json();
    }

    async post(params){

        const settings = {
            method: this.verbo,          
            body: params
        }

        let res = await fetch(this.API, settings);
        return res.json();    

    }

    async postSend(params){

        const settings = {
            method: this.verbo,          
            body: params
        }                 

        try {            
            let res = await fetch(this.API, settings);
            return res.json();    
        } catch (error) {
            console.log('error fetchpost2', error);
        } finally {
            spinner.innerHTML =  "";
            spinner.classList.remove('spinnerShow');
        }


    }

}
export default DB;