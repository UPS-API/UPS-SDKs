//import { Base } from "./base";
//import { AuthTokengenerator } from "./auth";
import { applyMixins } from "./utils";
import { AuthCodeService } from "src/AuthCodeService";

//class Tokengenerator extends Base {}
//interface Tokengenerator extends AuthTokengenerator {}

applyMixins(AuthCodeService, [AuthCodeService]);

//export default Tokengenerator;
export { AuthCodeService } from "src/AuthCodeService";
