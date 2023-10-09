import { cn } from "@/lib/utils";
import styles from "../../css/loading-dots.module.css";

const LoadingDots = ({
    color = "#000",
    size = "small",
}: {
    color?: string;
    size?: "small" | "large";
}) => {
    return (
        <span
            className={cn(size == "small" ? styles.loading2 : styles.loading)}
        >
            <span style={{ backgroundColor: color }} />
            <span style={{ backgroundColor: color }} />
            <span style={{ backgroundColor: color }} />
        </span>
    );
};

export default LoadingDots;

LoadingDots.defaultProps = {
    style: "small",
};
