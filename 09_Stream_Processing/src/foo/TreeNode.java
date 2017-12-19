package foo;

// Copyright 2017 Max Sprauer

import java.util.*;

public class TreeNode {
    private TreeNode parent;
    private ArrayList<TreeNode> children;
    private StringBuffer contents;

    public TreeNode(TreeNode parent)
    {
        this.parent = parent;
        this.children = new ArrayList<TreeNode> ();
        this.contents = new StringBuffer();
    }

    public void AddChild(TreeNode child)
    {
        this.children.add(child);
    }

    public TreeNode GetParent()
    {
        return parent;
    }

    public void AddChar(char x)
    {
        this.contents.append(x);
    }

    public int GetScore(int parentScore)
    {
        int subtreeScore = parentScore + 1;

        for (TreeNode n: this.children) {
            subtreeScore += n.GetScore(parentScore + 1);
        }

        return subtreeScore;
    }

    public String toString()
    {
        StringBuffer b = new StringBuffer("{");
        // b.append(this.contents);

        for (TreeNode n: this.children) {
            b.append(n.toString());
        }

        b.append("}");
        return b.toString();
    }
}
