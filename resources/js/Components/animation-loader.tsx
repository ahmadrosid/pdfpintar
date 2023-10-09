import { Loader2 } from "lucide-react";


export default function AnimationLoader (){
    return (
        <div className="flex items-center justify-center">
            <Loader2 className="animate-spin duration-3000" />
        </div>
    )
}