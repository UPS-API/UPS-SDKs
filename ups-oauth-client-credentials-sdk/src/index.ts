//import { Base } from "./base";
//import { AuthTokengenerator } from "./auth";
import { applyMixins } from "./utils";
import { ClientCredentialService } from "./ClientCredentialService";

//class Tokengenerator extends Base {}
//interface Tokengenerator extends AuthTokengenerator {}

applyMixins(ClientCredentialService, [ ClientCredentialService]);

//export default Tokengenerator;

export { ClientCredentialService } from "src/ClientCredentialService";
